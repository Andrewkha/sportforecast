<?php

namespace app\models\users;

use Yii;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;

/**
 * LoginForm is the model behind the login form.
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user;

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
            'rememberMe' => 'Запомнить',
        ];
    }

    /**
     * @return array the validation rules.
     */

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            //user is not blocked
            ['username', 'validateStatus']
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attributeName)
    {
        if ($this->hasErrors())
            return;

        $user = $this->getUser($this->username);
        if(!($user && Yii::$app->security->validatePassword($this->$attributeName, $user->password))) {
                $this->addError('password', 'Неверное имя пользователя или пароль');
                $this->username = '';
                $this->password = '';
        }
    }

    public function validateStatus($attributeName) {

        if ($this->hasErrors())
            return;

        $user = $this->getUser($this->username);
        if($user->active == Users::STATUS_BLOCKED) {
            $this->addError('username', 'Пользователь заблокирован');
            $this->username = '';
            $this->password = '';
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser($this->username);

            Yii::$app->user->on(\yii\web\User::EVENT_AFTER_LOGIN, function($event) {
                $event->identity->last_login = time();
                $event->identity->detachBehavior('TimestampBehavior');
                //$event->identity->updatedAtAttribute = 'last_login';
                //print_r($event->identity); exit;
                $event->identity->save();
            });
            return Yii::$app->user->login($user, $this->rememberMe ? 3600*24*7 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     */

    public function getUser($username)
    {
        if (!$this->_user) {
            $this->_user = Users::findOne(compact('username'));
        }

        return $this->_user;
    }
}
