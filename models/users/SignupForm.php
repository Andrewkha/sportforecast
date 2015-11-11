<?php

namespace app\models\users;

use Yii;
use yii\base\Model;
use app\models\users\Users;
use yii\web\UploadedFile;

/**
 * LoginForm is the model behind the login form.
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $password_repeat;
    public $avatar;
    public $first_name;
    public $last_name;
    public $verifyCode;


    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'email' => 'Email',
            'avatar' => 'Аватар',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'password' => 'Пароль',
            'password_repeat' => 'Подтверждение пароля',
            'verifyCode' => 'Введите символы'
        ];
    }

    /**
     * @return array the validation rules.
     */

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'email', 'password'], 'required'],
            ['email', 'email'],
            [['username'], 'string', 'max' => 30],
            [['password', 'password_repeat'], 'string', 'max' => 20],
            [['first_name', 'last_name'], 'string', 'max' => 50],

            ['password', function($attribute, $params) {
                if($this->password != $this->password_repeat)
                    $this->addError($attribute, 'Введенные пароли не совпадают');
            }],

            [['username', 'email'], 'unique', 'targetClass' => users::className()],

            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],

            [['avatar'], 'image', 'maxSize' => 1024*1024, 'tooBig' => 'Максимальный размер файла 1Мб'],
        ];
    }

    public function register() {

        $user = new Users();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->password = $this->password;
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->last_login = time();
        $user->avatar = UploadedFile::getInstance($this, 'avatar');

        $user->save();

        return Yii::$app->user->login($user, 3600*24*7);
    }

}
