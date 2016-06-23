<?php

namespace app\models\tournaments;


use app\models\games\Games;
use app\models\news\News;
use app\models\teams\Teams;
use app\models\forecasts\Top3TeamsForecast;
use app\models\users\UsersTournaments;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use Yii;
use app\models\traits\tournamentsTrait;
use DiDom\Document;
use app\components\parsing\ParsingManager;


/**
 * This is the model class for table "{{%tournaments}}".
 *
 * @property integer $id_tournament
 * @property string $tournament_name
 * @property integer $country
 * @property integer $num_tours
 * @property integer $startsOn
 * @property string $is_active
 * @property string $autoProcessURL
 * @property integer $enableAutoprocess
 *
 * @property TeamTournaments[] $teamTournaments
 * @property Countries $country0
 * @property UsersTournaments[] $usersTournaments
 */
class Tournaments extends \yii\db\ActiveRecord
{
    use tournamentsTrait;

    const NOT_STARTED = '0';
    const GOING = '1';
    const FINISHED = '2';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tournaments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tournament_name', 'country', 'num_tours', 'is_active', 'wfDueTo'], 'required'],
            [['country', 'num_tours', 'enableAutoprocess'], 'integer'],
            [['tournament_name',], 'string', 'max' => 255],
            ['autoProcessURL', 'url'],
            ['startsOn', 'date', 'format' => 'php:d.m.Y', 'timestampAttribute' => 'startsOn'],
            ['wfDueTo', 'date', 'format' => 'php:d.m.Y', 'timestampAttribute' => 'wfDueTo'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_tournament' => Yii::t('app', 'ID'),
            'tournament_name' => Yii::t('app', 'Название'),
            'country' => Yii::t('app', 'Страна'),
            'num_tours' => Yii::t('app', 'Количество туров'),
            'is_active' => Yii::t('app', 'Статус'),
            'startsOn' => 'Начало турнира',
            'wfDueTo' => 'Окончание приема прогнозов на призеров турнира',
            'enableAutoprocess' => 'Автозагрузка календаря',
            'autoProcessURL' => 'Источник данных',
        ];
    }

    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);

        if(isset($changedAttributes['is_active']) && $changedAttributes['is_active'] != self::FINISHED && $this->is_active == self::FINISHED) {

            News::updateAll(['archive' => News::ARCHIVE_TRUE], ['id_tournament' => $this->id_tournament] );

            //assigning Events for winners forecast
            Top3TeamsForecast::setEventForTournament($this->id_tournament);

            //adding additional points to the total points field of UserTournaments model
            $this->assignAdditionalPoints();
        }

        //if setting tournament back to active from finished, need to remove calculated additional points and clean additional forecast events

        if(isset($changedAttributes['is_active']) && $changedAttributes['is_active'] == self::FINISHED && $this->is_active == self::GOING) {

            //removing additional points to the total points field of UserTournaments model
            $this->removeAdditionalPoints();
            //assigning Events for winners forecast to NULL
            Top3TeamsForecast::clearEventForTournament($this->id_tournament);
        }
        
        if($insert) {

            $news = new News();
            $news->scenario = 'send';
            $news->id_tournament = 0;
            $news->subject = 'Добавлен новый турнир';
            $news->body = "<p>Для прогноза доступен новый турнир {$this->tournament_name}, первый тур которого состоится ".date('d.m.y', $this->startsOn)." </p>"
                ."<p>Вы также можете попробовать угадать призеров турнира и заработать дополнительные очки! Прогноз на призеров принимается до ".date('d.m.y', $this->wfDueTo)." </p>"
                ."<p>Спешите принять участие! Зайдите в <strong>Профиль->Мои турниры</strong> чтобы начать делать прогнозы</p>";

            $news->save();

        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamTournaments()
    {
        return $this->hasMany(TeamTournaments::className(), ['id_tournament' => 'id_tournament']);
    }

    public function getTeams()
    {
        return $this->hasMany(\app\models\teams\Teams::className(), ['id_team' => 'id_team'])->viaTable('{{%team_tournaments}}', ['id_tournament' => 'id_tournament']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry0()
    {
        return $this->hasOne(\app\models\countries\Countries::className(), ['id' => 'country']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersTournaments()
    {
        return $this->hasMany(UsersTournaments::className(), ['id_tournament' => 'id_tournament']);
    }

    public function getStatus() {

        return self::statuses()[$this->is_active];
    }

    public static function statuses() {

        return [
            self::NOT_STARTED => 'Не начался',
            self::GOING => 'Проходит',
            self::FINISHED => 'Закончен',
        ];
    }

    public function autoProcess()
    {
        $parser = ParsingManager::getParser($this);
        $parser->parse();
    }


    private function assignAdditionalPoints()
    {
        $userTournamentsModels = UsersTournaments::find()->with('winnersForecast')->where(['id_tournament' => $this->id_tournament])->all();

        foreach($userTournamentsModels as $one)
        {
            $one->points += $one->calculateAdditionalPoints();

            $one->save(false);
        }
    }

    private function removeAdditionalPoints()
    {
        $userTournamentsModels = UsersTournaments::find()->with('winnersForecast')->where(['id_tournament' => $this->id_tournament])->all();

        foreach($userTournamentsModels as $one)
        {
            $one->points -= $one->calculateAdditionalPoints();

            $one->save(false);
        }
    }
}
