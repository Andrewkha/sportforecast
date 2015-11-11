<?php

namespace app\models\users;

use Yii;
use yii\base\Model;
use app\models\users\Users;

/**
 * LoginForm is the model behind the login form.
 */
class passResetForm extends Model
{
    public $username;
    public $email;

    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'email' => 'Email',
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

            ['username',
                'exist', 'targetClass' => users::className(),
                'targetAttribute' => ['username', 'email'],
                'filter' => ['active' => users::STATUS_ACTIVE],
                'message' => 'Пользователя с таким email не существует'
            ],
        ];
    }

    public function passReset() {

        $user = users::findOne([
            'active' => Users::STATUS_ACTIVE,
            'username' => $this->username,
            'email' => $this->email
        ]);

        if($user) {
            if(empty($user->forgotten_password_code)) {
                $user->forgotten_password_code = Yii::$app->security->generateRandomString();
            }

            if($user->save()) {
                return \Yii::$app->mailer->compose('passReset',['user' => $user])
                    ->setFrom([Yii::$app->params['adminEmail'] => 'Sportforecast'])
                    ->setTo($user->email)
                    ->setSubject('Восстановление пароля')
                    ->send();
            }
        }

        return false;
    }
}
