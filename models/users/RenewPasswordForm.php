<?php

namespace app\models\users;

use yii\base\InvalidParamException;
use yii\base\Model;
use Yii;
use app\models\users\Users;

/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 5/25/15
 * Time: 6:29 PM
 * To change this template use File | Settings | File Templates.
 */

class RenewPasswordForm extends Model
{
    public $password;

    private $_user;

    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Неверный token сброса пароля');
        }
        $this->_user = users::findOne(['forgotten_password_code' => $token]);
        if (!$this->_user) {
            throw new InvalidParamException('Неверный token сброса пароля');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Resets password.
     *
     * @return boolean if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->password = $this->password;
        $user->forgotten_password_code = null;

        $user->save();

        return Yii::$app->user->login($user, 3600*24*7);
    }
}