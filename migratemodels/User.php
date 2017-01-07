<?php

namespace app\migratemodels;

use Yii;

/**
 * This is the model class for table "sf_user".
 *
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $email
 * @property string $firstName
 * @property string $lastName
 * @property string $avatar
 * @property string $forgottenPasswordCode
 * @property string $activationString
 * @property string $auth_key
 * @property integer $userStatus
 * @property integer $notificationsStatus
 * @property integer $created_on
 * @property integer $updated_on
 * @property integer $lastLogin
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sf_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userStatus', 'notificationsStatus', 'created_on', 'updated_on', 'lastLogin'], 'integer'],
            [['username', 'password', 'email', 'avatar', 'forgottenPasswordCode', 'activationString'], 'string', 'max' => 255],
            [['firstName', 'lastName'], 'string', 'max' => 50],
            [['auth_key'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
            'email' => 'Email',
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'avatar' => 'Avatar',
            'forgottenPasswordCode' => 'Forgotten Password Code',
            'activationString' => 'Activation String',
            'auth_key' => 'Auth Key',
            'userStatus' => 'User Status',
            'notificationsStatus' => 'Notifications Status',
            'created_on' => 'Created On',
            'updated_on' => 'Updated On',
            'lastLogin' => 'Last Login',
        ];
    }
}
