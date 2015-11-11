<?php

use yii\db\Schema;
use yii\db\Migration;

class m150702_102001_add_tournaments_columns_enable_autoprocessing extends Migration
{
    public function up()
    {
        $this->addColumn('sf_tournaments', 'enableAutoprocess', 'boolean');
        $this->addColumn('sf_tournaments', 'autoProcessURL', 'string');
    }

    public function down()
    {
        $this->dropColumn('sf_tournaments', 'enableAutoprocess');
        $this->dropColumn('sf_tournaments', 'autoProcessURL');
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
