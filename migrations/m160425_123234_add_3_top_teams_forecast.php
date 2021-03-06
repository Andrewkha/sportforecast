<?php

use yii\db\Migration;

class m160425_123234_add_3_top_teams_forecast extends Migration
{
    public function up()
    {
        $this->createTable('{{%top_3_teams_forecast}}',[
            'id' => $this->primaryKey(),
            'id_user' => $this->integer(11)->unsigned()->notNull(),
            'id_tournament' => $this->integer(11)->notNull(),
            'id_participant_team' => $this->integer(10)->unsigned()->notNull(),
            'forecasted_position' => $this->integer(1)->unsigned(),
            'time' => $this->integer(),
        ]);

        $this->createIndex('idx-topteams-user-tournament_team', '{{%top_3_teams_forecast}}', ['id_user', 'id_tournament', 'id_participant_team'], true);

        $this->addForeignKey('fk-topteams-id_user','{{%top_3_teams_forecast}}','id_user' ,'{{%users}}', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk-topteams-id_tournament','{{%top_3_teams_forecast}}','id_tournament' ,'{{%tournaments}}', 'id_tournament', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk-topteams-id_participant_team','{{%top_3_teams_forecast}}','id_participant_team' ,'{{%team_tournaments}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%top_3_teams_forecast}}');
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
