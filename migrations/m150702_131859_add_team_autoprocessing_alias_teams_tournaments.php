<?php

use yii\db\Schema;
use yii\db\Migration;

class m150702_131859_add_team_autoprocessing_alias_teams_tournaments extends Migration
{
    public function up()
    {
        $this->addColumn('sf_team_tournaments', 'alias', 'string');
    }

    public function down()
    {
        $this->dropColumn('sf_team_tournaments', 'alias');
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
