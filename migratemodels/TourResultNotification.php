<?php

namespace app\migratemodels;

use Yii;

/**
 * This is the model class for table "sf_tourresultnotification".
 *
 * @property integer $tournament_id
 * @property integer $tour
 * @property integer $date
 *
 * @property Tournament $tournament
 */
class TourResultNotification extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sf_tourresultnotification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tournament_id', 'tour'], 'required'],
            [['tournament_id', 'tour', 'date'], 'integer'],
            [['tournament_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tournament::className(), 'targetAttribute' => ['tournament_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tournament_id' => 'Tournament ID',
            'tour' => 'Tour',
            'date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament()
    {
        return $this->hasOne(Tournament::className(), ['id' => 'tournament_id']);
    }
}
