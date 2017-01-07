<?php

namespace app\migratemodels;

use Yii;

/**
 * This is the model class for table "sf_game".
 *
 * @property integer $id
 * @property integer $tournament_id
 * @property integer $teamHome_id
 * @property integer $teamGuest_id
 * @property integer $tour
 * @property integer $date
 * @property integer $scoreHome
 * @property integer $scoreGuest
 *
 * @property Team $teamGuest
 * @property Team $teamHome
 * @property Tournament $tournament
 */
class Game extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sf_game';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tournament_id', 'teamHome_id', 'teamGuest_id', 'tour', 'date', 'scoreHome', 'scoreGuest'], 'integer'],
            [['teamHome_id', 'teamGuest_id', 'tour'], 'required'],
            [['teamGuest_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['teamGuest_id' => 'id']],
            [['teamHome_id'], 'exist', 'skipOnError' => true, 'targetClass' => Team::className(), 'targetAttribute' => ['teamHome_id' => 'id']],
            [['tournament_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tournament::className(), 'targetAttribute' => ['tournament_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tournament_id' => 'Tournament ID',
            'teamHome_id' => 'Team Home ID',
            'teamGuest_id' => 'Team Guest ID',
            'tour' => 'Tour',
            'date' => 'Date',
            'scoreHome' => 'Score Home',
            'scoreGuest' => 'Score Guest',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamGuest()
    {
        return $this->hasOne(Team::className(), ['id' => 'teamGuest_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamHome()
    {
        return $this->hasOne(Team::className(), ['id' => 'teamHome_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament()
    {
        return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
    }
}
