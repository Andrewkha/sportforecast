<?php

use yii\db\Migration;
use app\models\users\Users;
use app\migratemodels\User;

class m170107_150054_populate_new_user_table extends Migration
{
    public function up()
    {
        $old = Users::find()->all();

        foreach ($old as $one)
        {
            $new = new User();
            $new->id = $one->id;
            $new->username = $one->username;
            $new->password = $one->password;
            $new->email = $one->email;
            $new->firstName = $one->first_name;
            $new->lastName = $one->last_name;
            $new->avatar = $one->avatar;
            $new->forgottenPasswordCode = $one->forgotten_password_code;
            $new->activationString = NULL;
            $new->auth_key = $one->auth_key;
            $new->userStatus = $one->active;
            $new->notificationsStatus = $one->notifications;
            $new->created_on = $one->created_on;
            $new->updated_on = $one->updated_on;
            $new->lastLogin = $one->last_login;

            $new->save(false);
        }
    }

    public function down()
    {
        echo "m170107_150054_populate_new_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
