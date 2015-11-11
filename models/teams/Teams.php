<?php

namespace app\models\teams;

use app\components\fileUploadBehavior;
use Yii;
use app\models\countries\Countries;
use app\models\tournaments\TeamTournaments;

/**
 * This is the model class for table "{{%teams}}".
 *
 * @property integer $id_team
 * @property string $team_name
 * @property integer $country
 * @property string $team_logo
 *
 * @property TeamTournaments[] $teamTournaments
 * @property Countries $country0
 */
class Teams extends \yii\db\ActiveRecord
{

    const TEAMS_LOGO_UPLOAD_PATH = 'images/logos';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%teams}}';
    }

    public function behaviors() {

        return [
            'fileUpload' =>
                [
                    'class' => fileUploadBehavior::className(),
                    'toAttribute' => 'team_logo',
                    'imagePath' => self::TEAMS_LOGO_UPLOAD_PATH,
                    'default' => 'nologo.jpeg',
                    'prefix' => 'time',
                ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['team_name', 'country'], 'required'],
            [['country'], 'integer'],
            [['team_name'], 'string', 'max' => 50],
            [['team_logo'], 'image', 'maxSize' => 1024*1024, 'tooBig' => 'Максимальный размер файла 1Мб',],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_team' => 'ID',
            'team_name' => 'Название',
            'country' => 'Страна',
            'team_logo' => 'Логотип',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamTournaments()
    {
        return $this->hasMany(TeamTournaments::className(), ['id_team' => 'id_team']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry0()
    {
        return $this->hasOne(\app\models\countries\Countries::className(), ['id' => 'country']);
    }

    //get the list of teams for the specific country and not included into $participants (other words - get candidates for the tournament)

    public static function getTeamCandidates($country, $participants)
    {

        return self::find()->where(['not in', 'id_team', $participants])->andFilterWhere(['country' => $country->id])->OrderBy(['team_name' => SORT_ASC])->all();
    }

    public static function getPath() {

        return  Yii::getAlias('@web/' . self::TEAMS_LOGO_UPLOAD_PATH);
    }
}
