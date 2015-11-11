<?php

namespace app\models\countries;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%countries}}".
 *
 * @property integer $id
 * @property string $country
 *
 * @property Teams[] $teams
 * @property Tournaments[] $tournaments
 */
class Countries extends \yii\db\ActiveRecord
{

    //indicate that tournament is international
    const INTERNATIONAL = 'Международный';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%countries}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country'], 'required'],
            [['country'], 'string', 'max' => 100],
            [['country'], 'unique', 'message' => 'Такая страна уже есть']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country' => 'Страна',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(Teams::className(), ['country' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournaments()
    {
        return $this->hasMany(Tournaments::className(), ['country' => 'id']);
    }

    public static function getCountriesArray() {

        $countries = Countries::find()->orderBy(['country' => SORT_ASC])->asArray()->all();
        return ArrayHelper::map($countries, 'id', 'country');
    }

}
