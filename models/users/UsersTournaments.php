<?php

namespace app\models\users;

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

    public function deleteForecasts() {

        $games = ArrayHelper::getColumn(Result::find()
            ->where(['id_tournament' => $this->id_tournament])
            ->all(), 'id_game');

        return Forecasts::deleteAll(['and', ['in', 'id_game', $games], ['id_user' => $this->id_user]]);
    }
}