<?php

namespace app\models\users;

use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;
use app\components\fileUploadBehavior;
use app\models\forecasts\Forecasts;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%users}}".
 *
 * @property string $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $forgotten_password_code
 * @property string $created_on
 * @property string $last_login
 * @property integer $active
 * @property string $first_name
 * @property string $last_name
 * @property string $avatar
 * @property integer $notifications
 * @property string $auth_key
 *
 * @property Forecasts[] $forecasts
 * @property News[] $news
 * @property UsersGroups[] $usersGroups
 * @property UsersTournaments[] $usersTournaments
 */
class users extends \yii\db\ActiveRecord implements IdentityInterface
{
    const AVATAR_PATH = 'images/avatars';
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 0;

    const SUBSCRIBED = 1;
    const NOT_SUBSCRIBED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */

    public function behaviors() {

        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_on',
                'updatedAtAttribute' => 'updated_on',
            ],
            'fileUpload' =>
                [
                    'class' => fileUploadBehavior::className(),
                    'toAttribute' => 'avatar',
                    'imagePath' => self::AVATAR_PATH,
                    'default' => 'default.jpg',
                    'prefix' => 'username',
                ],
        ];
    }

    public static function getPath() {

        return  Yii::getAlias('@web/' . self::AVATAR_PATH);
    }

    public function rules()
    {
        return [
            [['username', 'password', 'email'], 'required'],
            [['username', 'email'], 'unique'],
            ['email', 'email'],
            [['active', 'notifications', 'rehash'], 'integer'],
            [['username'], 'string', 'max' => 100],
            [['password'], 'string', 'max' => 80],
            [['forgotten_password_code'], 'string', 'max' => 40],
            [['first_name', 'last_name'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
            [['avatar'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'password' => 'Пароль',
            'email' => 'Email',
            'forgotten_password_code' => 'Forgotten Password Code',
            'created_on' => 'Дата регистрации',
            'last_login' => 'Последний вход',
            'active' => 'Статус',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'avatar' => 'Аватар',
            'notifications' => 'Подписка на новости',
            'auth_key' => 'Auth Key',
        ];
    }

    //getting possible statuses
    public static function getStatuses() {

        return [
            self::STATUS_ACTIVE => 'Активен',
            self::STATUS_BLOCKED => 'Заблокирован'
        ];
    }

    //get friendly status name
    public function getStatus() {

        $statuses = self::getStatuses();
        return isset($statuses[$this->active])? $statuses[$this->active] : '';
    }

    //getting possible subscription options
    public static function getSubscription() {

        return [
            self::SUBSCRIBED => 'Активна',
            self::NOT_SUBSCRIBED => 'Неактивна'
        ];
    }

    //get friendly status name
    public function getSubscriptionStatus() {

        $statuses = self::getSubscription();
        return isset($statuses[$this->notifications])? $statuses[$this->notifications] : '';
    }

    public function beforeSave($insert) {

        $return = parent::beforeSave($insert);

        if($this->isAttributeChanged('password') && $this->scenario != 'rehash')
            $this->password = Yii::$app->security->generatePasswordHash($this->password);
        if($insert)
            $this->auth_key = Yii::$app->security->generateRandomString();
        return $return;
    }

    //if a user participates in the tournament

    public function isUserParticipatesInTournament($tournament) {

        return UsersTournaments::find()
            ->where(['id_user' => $this->id])
            ->andWhere(['id_tournament' => $tournament])
            ->exists();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getForecasts()
    {
        return $this->hasMany(Forecasts::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNews()
    {
        return $this->hasMany(News::className(), ['author' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersTournaments()
    {
        return $this->hasMany(UsersTournaments::className(), ['id_user' => 'id']);
    }

    //implementing methods of the interface

    public function getId() {

        return $this->id;
    }

    public static function findIdentity($id) {

        return static::findOne($id);
    }

    public function getAuthKey() {

        return $this->auth_key;
    }

    public function validateAuthKey($authKey) {

        return $this->getAuthKey() === $authKey;
    }

    public static function findIdentityByAccessToken ($token, $type = null) {

        throw new NotSupportedException('Для аутентификации нужно ввести логин/пароль');
    }
}
