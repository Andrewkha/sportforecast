<?php

namespace app\models\users;

use app\models\query\UsersTournamentsQuery;
use Yii;
use app\models\tournaments\Tournaments;
use app\models\result\Result;
use app\models\forecasts\Forecasts;
use yii\helpers\ArrayHelper;
use app\models\traits\usersTournamentsTrait;


/**
 * This is the model class for table "{{%users_tournaments}}".
 *
 * @property string $id
 * @property string $id_user
 * @property integer $id_tournament
 * @property integer $notification
 *
 * @property Tournaments $idTournament
 * @property Users $idUser
 */
class UsersTournaments extends \yii\db\ActiveRecord
{

    use usersTournamentsTrait;

    //additional properties for tracking user position and points in the tournament as well as leader's position and points

    public $userPosition = NULL;
    public $userPoints = NULL;
    public $leader = NULL;
    public $leaderPoints = NULL;

    const NOTIFICATION_ENABLED = 1;
    const NOTIFICATION_DISABLED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users_tournaments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'id_tournament'], 'required'],
            [['id_user', 'id_tournament', 'notification'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Id User',
            'id_tournament' => 'Id Tournament',
            'notification' => 'Уведомления',
            'userPosition' => 'Место',
            'userPoints' => 'Очки',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTournament()
    {
        return $this->hasOne(Tournaments::className(), ['id_tournament' => 'id_tournament']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'id_user']);
    }

    public function getTotalPoints()
    {
        //if tournament not finished - from model, else add points for guessing 3 top
        if($this->idTournament->is_active != Tournaments::FINISHED)
            return $this->points;
        else
            return $this->points;
        //todo  add additional forecast once done
    }

    public function getPosition()
    {
        $models = self::find()->forecastersStandings($this->id_tournament)->all();

        foreach ($models as $k => $one)
            if($one->id_tournament == $this->id_tournament && $one->id_user == $this->id_user)
                return $k + 1;

        return '-';
    }

    //getting possible subscription statuses
    public static function getSubscription() {

        return [
            self::NOTIFICATION_DISABLED => 'Неактивно',
            self::NOTIFICATION_ENABLED => 'Активно'
        ];
    }

    //get friendly ыгиыскшзешщт status name
    public function getSubscriptionStatus() {

        $statuses = self::getSubscription();
        return isset($statuses[$this->notification])? $statuses[$this->notification] : '';
    }

    /**
     * @inheritdoc
     * @return UsersTournamentsQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersTournamentsQuery(get_called_class());
    }

    public function deleteForecasts() {

        $games = ArrayHelper::getColumn(Result::find()
            ->where(['id_tournament' => $this->id_tournament])
            ->all(), 'id_game');

        return Forecasts::deleteAll(['and', ['in', 'id_game', $games], ['id_user' => $this->id_user]]);
    }

    public static function topThreeForecastersForTournament($tournament)
    {
        $result = self::find()
            ->where(['id_tournament' => $tournament])
            ->with('idUser')
            ->orderBy(['points' => SORT_DESC])
            ->limit(3)
            ->all();

        return $result;
    }
}
