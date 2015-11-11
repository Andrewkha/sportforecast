<?php

use yii\db\Schema;
use yii\db\Migration;


class m150409_120111_alter_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('sf_users', 'updated_on', 'integer unsigned');
    }

    public function down()
    {
        echo "m150409_120111_alter_user_table cannot be reverted.\n";

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
