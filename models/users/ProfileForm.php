<?php

namespace app\models\users;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use app\models\users\Users;

/**
 * LoginForm is the model behind the login form.
 */
class ProfileForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_repeat;
    public $avatar;
    public $old_avatar;
    public $first_name;
    public $last_name;
    public $notifications;

    private $_user;

    public function __construct($config = [])
    {

        $this->_user = Users::findOne(['id' => Yii::$app->user->identity->id]);

        $this->username = $this->_user->username;
        $this->email = $this->_user->email;
        $this->old_avatar = $this->_user->avatar;
        $this->first_name = $this->_user->first_name;
        $this->last_name = $this->_user->last_name;
        $this->notifications = $this->_user->notifications;

        parent::__construct($config);
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'email' => 'Email',
            'avatar' => 'Изменить аватар',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'password' => 'Пароль',
            'password_repeat' => 'Подтверждение пароля',
            'notifications' => 'Подписка на новости',
        ];
    }

    /**
     * @return array the validation rules.
     */

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'email'], 'required'],
            ['email', 'email'],
            [['username'], 'string', 'max' => 30],
            [['password', 'password_repeat'], 'string', 'max' => 20],
            [['first_name', 'last_name'], 'string', 'max' => 50],
            ['notifications', 'integer'],

            ['password', function($attribute, $params) {
                if($this->password != $this->password_repeat)
                    $this->addError($attribute, 'Введенные пароли не совпадают');
            }],

            [['username', 'email'], 'unique', 'targetClass' => users::className(), 'filter' => [
                'not', ['id' => Yii::$app->user->identity->id],
            ]],

            [['avatar'], 'image', 'maxSize' => 1024*1024, 'tooBig' => 'Максимальный размер файла 1Мб'],
        ];
    }

    public function getFileUrl() {

        return Yii::getAlias('@web/'.Users::AVATAR_PATH).'/'.$this->old_avatar;
    }

    public function edit() {

        $user = $this->_user;

        $user->username = $this->username;
        $user->email = $this->email;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->notifications = $this->notifications;

        $user->avatar = UploadedFile::getInstance($this,'avatar');

        //if new password provided
        if($this->password) {

            $user->password = $this->password;
        }

        return $user->save();
    }
}
