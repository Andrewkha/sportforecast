<?php

use yii\db\Migration;

class m160429_122212_drop_composite_index_from_winners_forecast_table extends Migration
{
    public function up()
    {

        $this->dropForeignKey('fk-topteams-id_user', '{{%top_3_teams_forecast}}');
        $this->dropForeignKey('fk-topteams-id_tournament', '{{%top_3_teams_forecast}}');
        $this->dropForeignKey('fk-topteams-id_participant_team', '{{%top_3_teams_forecast}}');

        $this->dropIndex('idx-topteams-user-tournament_team', '{{%top_3_teams_forecast}}');

        $this->createIndex('idx-topteams-user-tournament_team', '{{%top_3_teams_forecast}}', ['id_user', 'id_tournament', 'id_participant_team'], false);

        $this->addForeignKey('fk-topteams-id_user','{{%top_3_teams_forecast}}','id_user' ,'{{%users}}', 'id', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk-topteams-id_tournament','{{%top_3_teams_forecast}}','id_tournament' ,'{{%tournaments}}', 'id_tournament', 'CASCADE', 'CASCADE');

        $this->addForeignKey('fk-topteams-id_participant_team','{{%top_3_teams_forecast}}','id_participant_team' ,'{{%team_tournaments}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
    }
}
