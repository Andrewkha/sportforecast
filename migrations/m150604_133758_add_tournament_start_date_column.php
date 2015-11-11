<?php

use yii\db\Schema;
use yii\db\Migration;

class m150604_133758_add_tournament_start_date_column extends Migration
{
    public function up()
    {
        $this->addColumn('sf_tournaments', 'startsOn', 'integer unsigned');
    }

    public function down()
    {
        echo "m150604_133758_add_tournament_start_date_column cannot be reverted.\n";

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
