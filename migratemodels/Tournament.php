<?php

namespace app\migratemodels;

use Yii;

/**
 * This is the model class for table "{{%tournament}}".
 *
 * @property integer $id
 * @property string $tournament
 * @property integer $country_id
 * @property integer $type
 * @property integer $tours
 * @property integer $status
 * @property integer $starts
 * @property integer $autoprocess
 * @property string $autoprocessURL
 * @property integer $winnersForecastDue
 *
 * @property Country $country
 */
class Tournament extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tournament}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tournament', 'country_id', 'type', 'tours', 'status'], 'required'],
            [['country_id', 'type', 'tours', 'status', 'starts', 'autoprocess', 'winnersForecastDue'], 'integer'],
            [['tournament'], 'string', 'max' => 150],
            [['autoprocessURL'], 'string', 'max' => 255],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tournament' => 'Tournament',
            'country_id' => 'Country ID',
            'type' => 'Type',
            'tours' => 'Tours',
            'status' => 'Status',
            'starts' => 'Starts',
            'autoprocess' => 'Autoprocess',
            'autoprocessURL' => 'Autoprocess Url',
            'winnersForecastDue' => 'Winners Forecast Due',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }
}
