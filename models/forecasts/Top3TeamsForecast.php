<?php

namespace app\models\forecasts;

use app\models\result\Result;
use app\models\tournaments\TeamTournaments;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%top_3_teams_forecast}}".
 *
 * @property integer $id
 * @property string $id_user
 * @property integer $id_tournament
 * @property string $id_participant_team
 * @property string $forecasted_position
 * @property integer $time
 * @property integer $event
 *
 * @property TeamTournaments $idParticipantTeam
 * @property Tournaments $idTournament
 * @property Users $idUser
 */
class Top3TeamsForecast extends \yii\db\ActiveRecord
{

    /**
     * constants describing the forecast event
     */
    const TEAM_IN_TOP_3 = 1;
    const TEAM_POSITION = 2;
    const ALL_3_WINNERS = 3;

    /**
     * constants for assigning points
     */

    const POINTS_TEAM_IN_TOP_3 = 10;
    const POINTS_TEAM_POSITION = 20;
    const POINTS_ALL_3_WINNERS = 20;

    public function getPointsForEvent()
    {
        switch($this->event)
        {
            case self::TEAM_IN_TOP_3:
                return self::POINTS_TEAM_IN_TOP_3;
            case self::TEAM_POSITION:
                return self::POINTS_TEAM_POSITION;
            case self::ALL_3_WINNERS:
                return self::POINTS_ALL_3_WINNERS;
        }
    }

    public static function getAdditionalPoints($user, $tournament)
    {
        $forecast = self::find()->where(['id_tournament' => $tournament])->andWhere(['id_user' => $user])->all();

        $summ = 0;

        foreach ($forecast as $one)
        {
            $summ += $one->pointsForEvent;
        }
        return $summ;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%top_3_teams_forecast}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'id_tournament', 'id_participant_team', 'forecasted_position'], 'required'],
            [['id_user', 'id_tournament', 'id_participant_team', 'forecasted_position', 'time'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Пользователь',
            'id_tournament' => 'Турнир',
            'id_participant_team' => 'Команда',
            'forecasted_position' => 'Место',
            'time' => 'Time',
        ];
    }

    public function behaviors() {

        return [
            'TimestampBehavior' =>
                [
                    'class' => TimestampBehavior::className(),
                    'createdAtAttribute' => 'time',
                    'updatedAtAttribute' => 'time',
                ],
            'BlameableBehavior' =>
                [
                    'class' => BlameableBehavior::className(),
                    'createdByAttribute' => 'id_user',
                    'updatedByAttribute' => 'id_user',
                ]
            ];
        }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdParticipantTeam()
    {
        return $this->hasOne(\app\models\tournaments\TeamTournaments::className(), ['id' => 'id_participant_team']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTournament()
    {
        return $this->hasOne(\app\models\tournaments\Tournaments::className(), ['id_tournament' => 'id_tournament']);
    }

    public function getTeam()
    {
        return $this->hasOne(TeamTournaments::className(),['id' => 'id_participant_team'] );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdUser()
    {
        return $this->hasOne(\app\models\users\Users::className(), ['id' => 'id_user']);
    }

    /**
     * @inheritdoc
     * @return \app\models\query\Top3TeamsForecastQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\Top3TeamsForecastQuery(get_called_class());
    }
    
    public static function setEventForTournament($id_tournament)
    {
        $winners = array_keys(ArrayHelper::index(Result::getWinners($id_tournament), 'participant'));

        foreach($winners as $position => $team)
        {
            self::updateAll(['event' => self::TEAM_IN_TOP_3], ['and', ['id_participant_team' => $team], ['id_tournament' => $id_tournament], ['not', ['forecasted_position' => $position + 1]]]);

            self::updateAll(['event' => self::TEAM_POSITION], ['and', ['id_participant_team' => $team], ['id_tournament' => $id_tournament], ['forecasted_position' => $position + 1]]);

            self::updateAll(['event' => NULL], ['and', ['id_tournament' => $id_tournament], ['not in', 'id_participant_team', $winners]]);
        }
    }

    public static function clearEventForTournament($id_tournament)
    {
        self::updateAll(['event' => NULL], ['id_tournament' =>$id_tournament]);
    }
    
    public static function getClarifications($user, $tournament)
    {
        $details = [];
        $models = self::find()->where(['id_user' => $user, 'id_tournament' => $tournament])->with('team.idTeam')->all();

        $bonus = 0;

        foreach ($models as $one)
        {
            if($one->event == self::TEAM_IN_TOP_3)
                $details[] = "Попадание в тройку призеров команды {$one->team->idTeam->team_name} - ".self::POINTS_TEAM_IN_TOP_3." очков";

            if($one->event == self::TEAM_POSITION)
                $details[] = "{$one->forecasted_position}-е место команды {$one->team->idTeam->team_name} - ".self::POINTS_TEAM_POSITION." очков";

            $bonus += $one->event;
        }

        if($bonus == 3*self::TEAM_POSITION)
            $details[] = "Дополнительный бонус за правильно угаданную тройку призеров ".Top3TeamsForecast::POINTS_ALL_3_WINNERS. " очков";

        return $details;
    }
}
