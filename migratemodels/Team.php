<?php

namespace app\migratemodels;

use Yii;

/**
 * This is the model class for table "{{%team}}".
 *
 * @property integer $id
 * @property string $team
 * @property integer $country_id
 * @property string $logo
 *
 * @property Country $country
 */
class Team extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%team}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team', 'country_id'], 'required'],
            [['country_id'], 'integer'],
            [['team'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 255],
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
            'team' => 'Team',
            'country_id' => 'Country ID',
            'logo' => 'Logo',
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
