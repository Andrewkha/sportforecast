<?php

use yii\db\Schema;
use yii\db\Migration;

class m150415_135805_drop_legacy_role_table extends Migration
{
    public function up()
    {
        $this->dropTable('sf_users_groups');
    }

    public function down()
    {
        echo "m150415_135805_drop_legacy_role_table cannot be reverted.\n";

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
