<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\users\Users;

class m150409_121437_generate_auth_keys_for_all_users extends Migration
{
    public function up()
    {
        $users = users::find()->all();

        foreach($users as $user) {

            $user->auth_key = Yii::$app->getSecurity()->generateRandomString();
            $user->save();
        }
    }

    public function down()
    {
        echo "m150409_121437_generate_auth_keys_for_all_users cannot be reverted.\n";

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
