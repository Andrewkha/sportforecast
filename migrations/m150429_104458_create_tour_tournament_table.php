<?php

use yii\db\Schema;
use yii\db\Migration;

class m150429_104458_create_tour_tournament_table extends Migration
{
    public function up()
    {
        $this->createTable('sf_reminders_user_tournament_tour', [
            'id' => 'pk',
            'user' => Schema::TYPE_INTEGER.' UNSIGNED',
            'tournament' => Schema::TYPE_INTEGER,
            'tour' => Schema::TYPE_INTEGER,
            'reminders' => Schema::TYPE_SMALLINT,
            'date' => Schema::TYPE_INTEGER,
        ]);

        $this->addForeignKey('fk_user', 'sf_reminders_user_tournament_tour', 'user', 'sf_users', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_tournament', 'sf_reminders_user_tournament_tour', 'tournament', 'sf_tournaments', 'id_tournament', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('sf_reminders_user_tournament_tour');
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
