<?php

namespace app\models\forecasts;

use Yii;

/**
 * This is the model class for table "{{%top_3_teams_forecast}}".
 *
 * @property integer $id
 * @property string $id_user
 * @property integer $id_tournament
 * @property string $id_participant_team
 * @property string $forecasted_position
 * @property integer $time
 *
 * @property TeamTournaments $idParticipantTeam
 * @property Tournaments $idTournament
 * @property Users $idUser
 */
class Top3TeamsForecast extends \yii\db\ActiveRecord
{
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
            'id_user' => 'Id User',
            'id_tournament' => 'Id Tournament',
            'id_participant_team' => 'Id Participant Team',
            'forecasted_position' => 'Forecasted Position',
            'time' => 'Time',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdParticipantTeam()
    {
        return $this->hasOne(TeamTournaments::className(), ['id' => 'id_participant_team']);
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

    /**
     * @inheritdoc
     * @return \app\models\query\Top3TeamsForecastQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\Top3TeamsForecastQuery(get_called_class());
    }
}
