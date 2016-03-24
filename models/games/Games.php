<?php

namespace app\models\games;

use Yii;

use app\models\tournaments\Tournaments;
use app\models\tournaments\TourResultNotifications;
use app\models\tournaments\TeamTournaments;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use app\models\forecasts\Forecasts;
use app\models\result\Result;
use PHPExcel_IOFactory;
use app\models\traits\gameTrait;


/**
 * This is the model class for table "{{%games}}".
 *
 * @property integer $id_game
 * @property string $id_team_home
 * @property string $id_team_guest
 * @property integer $score_home
 * @property integer $score_guest
 * @property integer $points_home
 * @property integer $points_guest
 * @property integer $tour
 * @property string $date_time_game
 * @property integer $notificationSent
 *
 * @property Forecasts[] $forecasts
 * @property TeamTournaments $idTeamGuest
 * @property TeamTournaments $idTeamHome
 * @property mixed competitors
 */
class Games extends ActiveRecord
{
    use gameTrait;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%games}}';
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['id_team_home', 'id_team_guest', 'tour'], 'required'],
            [['id_team_home', 'id_team_guest', 'tour'], 'integer'],
            ['id_team_guest', 'compare', 'compareAttribute' => 'id_team_home', 'operator' => '!=', 'message' => 'Выберете разные команды'],
            ['id_team_home', 'compare', 'compareAttribute' => 'id_team_guest', 'operator' => '!=', 'message' => 'Выберете разные команды'],
            ['date_time_game', 'date', 'format' => 'php:d.m.y H:i', 'timestampAttribute' => 'date_time_game'],
            [['score_home', 'score_guest'], 'integer', 'min' => 0, 'max' => 99],
            [['id_team_home', 'id_team_guest', 'tour', 'date_time_game'], 'ifGameExists', 'on' => 'addGame'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_game' => Yii::t('app', 'ID игры'),
            'id_team_home' => Yii::t('app', 'Команда хозяев'),
            'id_team_guest' => Yii::t('app', 'Команда гостей'),
            'score_home' => Yii::t('app', 'Счет хозяев'),
            'score_guest' => Yii::t('app', 'Счет гостей'),
            'points_home' => Yii::t('app', 'Очки хозяев'),
            'points_guest' => Yii::t('app', 'Очки гостей'),
            'tour' => Yii::t('app', 'Тур'),
            'date_time_game' => Yii::t('app', 'Дата матча'),
            'notificationSent' => Yii::t('app', 'Notification Sent'),
        ];
    }

    public function getCompetitors() {

        return $this->idTeamHome->idTeam->team_name." - ".$this->idTeamGuest->idTeam->team_name;
    }

    public function getTournament()
    {
        return Result::findOne(['id_game' => $this->id_game])->tournament_name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForecasts()
    {
        return $this->hasMany(Forecasts::className(), ['id_game' => 'id_game']);
    }

    public function getResult() {

        return $this->hasOne(Result::className(), ['id_game' => 'id_game']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTeamGuest()
    {
        return $this->hasOne(TeamTournaments::className(), ['id' => 'id_team_guest']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTeamHome()
    {
        return $this->hasOne(TeamTournaments::className(), ['id' => 'id_team_home']);
    }

    public function beforeSave($insert) {

        if(parent::beforeSave($insert)) {

            //assigning teams points
            $this->getGamePoints();

            return true;
        } else {

            return false;
        }
    }

    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);

        //updating the forecast points for the game
        if(!$insert) {

            Forecasts::setForecastPoints($this->id_game, $this->score_home, $this->score_guest);
        }

        //getting the tournament number
        $tournament = ArrayHelper::getValue(Result::find()
            ->where(['id_game' => $this->id_game])
            ->one(), 'id_tournament');

        //if tour finished, need to send notifications (if not already sent) and put tournament as finished if it was last tour

        /**
         * @var $tournamentModel \app\models\tournaments\Tournaments
         */
        if(self::isTourFinished($tournament, $this->tour)) {

            $tournamentModel = Tournaments::findOne($tournament);

            if($tournamentModel->num_tours == $this->tour) {
                $tournamentModel->is_active = Tournaments::FINISHED;
                $tournamentModel->save(false);
            }

            if(!TourResultNotifications::ifExists($this->tour, $tournament)) {

                self::sendTourResults($this->tour, $tournament);

                $notification = new TourResultNotifications();
                $notification->tour = $this->tour;
                $notification->tournament = $tournament;
                $notification->save();
            }
        }
    }


    //check if there's a game for these teams with the same tour or on the same date - validator

    /**
     * @param $attribute
     * @param $params
     */
    public function ifGameExists($attribute, $params) {

        if(Games::find()->where(['id_team_home' => $this->id_team_home])->andWhere(['id_team_guest' => $this->id_team_guest])->andWhere(['tour' => $this->tour])->one() ||
        Games::find()->where(['id_team_home' => $this->id_team_home])->andWhere(['id_team_guest' => $this->id_team_guest])->andWhere(['date_time_game' => $this->date_time_game])->one())
            $this->addError('id_team_home', "Игра $this->competitors в этом туре уже существует");
    }

    private function getGamePoints() {

        if($this->score_home !== '' && $this->score_guest !== '') {
            if($this->score_home > $this->score_guest) {
                $this->points_home = 3;
                $this->points_guest = 0;
                return;
            }
            elseif($this->score_home == $this->score_guest) {
                $this->points_home = 1;
                $this->points_guest = 1;
                return;
            }
            $this->points_home = 0;
            $this->points_guest = 3;
            return;
        } else {
            $this->points_home = NULL;
            $this->points_guest = NULL;
            return;
        }
    }

    //upload games from Excel

    /**
     * @param $file
     * @return mixed
     */
    public static function uploadExcel($file) {

        $excel = PHPExcel_IOFactory::load($file);
        $workSheet = $excel->getActiveSheet();
        $success = 0;
        $failure = '';

        foreach($workSheet->getRowIterator(2) as $row) {

            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach($cellIterator as $cell) {
                $cellData[] = $cell->getValue();
            }

            if(isset($cellData[0]) && isset($cellData[1])) {
                $game = new Games();
                $game->id_team_home = $cellData[0];
                $game->id_team_guest = $cellData[1];
                $game->tour = $cellData[2];

                //getting time difference between local place and greenwich to enable correct time offset with PHPExcel
                $tz = new \DateTimeZone(date_default_timezone_get());
                $d = new \DateTime("now");
                $offset = $tz->getOffset($d);

                $game->date_time_game = \PHPExcel_Shared_date::ExcelToPHP($cellData[3])-$offset;
                $game->score_home = $cellData[4];
                $game->score_guest = $cellData[5];

                if($game->save()) {
                    $success += 1;
                } else {
                    $failure .= "При загрузке игры $game->competitors произошла ошибка ".$game->getFirstError('id_team_home')."<br>";
                }
            }
            unset($cellData);
        }

        if(empty($failure))
            $return['success'] = "Все $success записей успешно загружены";

        else {
            $return['failure'] = $failure;
        }

        $excel->disconnectWorksheets();
        unset($excel);

        return $return;
    }
}
