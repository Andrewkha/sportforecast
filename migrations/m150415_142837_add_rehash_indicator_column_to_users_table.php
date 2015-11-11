<?php

use yii\db\Schema;
use yii\db\Migration;
use app\models\users\Users;

class m150415_142837_add_rehash_indicator_column_to_users_table extends Migration
{
    public function up()
    {

        $users = Users::find()->all();
        foreach($users as &$one) {

            $one->rehash = 0;
            $one->save();
        }
    }

    public function down()
    {
        echo "m150415_142837_add_rehash_indicator_column_to_users_table cannot be reverted.\n";

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
