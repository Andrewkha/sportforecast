<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 4/15/15
 * Time: 6:32 PM
 * To change this template use File | Settings | File Templates.
 */

namespace app\models\users;
use Yii;
use yii\base\Model;


class RehashFrom extends Model {

    public $username;
    public $password;

    private $_user;

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
        ];
    }

    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            //user exists
            ['username', 'validateExistence'],
            //user already rehashed
            ['username', 'validateHash'],
        ];
    }

    public function validateExistence($attributeName)
    {
        if ($this->hasErrors())
            return;

        $user = $this->getUser($this->username);
        if(!($user)) {
            $this->addError('username', 'Неверное имя пользователя или пароль');
            $this->username = '';
            $this->password = '';
        }
    }

    public function validateHash($attributeName)
    {
        if ($this->hasErrors())
            return;

        $user = $this->getUser($this->username);
        if($user->rehash == 1) {
            $this->addError('username', 'Вы уже выполнили данную процедуру');
        }
    }

    public function rehash()
    {
        if ($this->validate()) {

            $user = $this->getUser($this->username);
            $user->password = Yii::$app->security->generatePasswordHash($this->password);
            $user->auth_key = Yii::$app->security->generateRandomString();
            $user->rehash = 1;
            $user->save();
            return true;

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