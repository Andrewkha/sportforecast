<?php

use yii\db\Migration;
use app\models\tournaments\TeamTournaments;
use app\migratemodels\TeamTournament;

class m170107_113418_populate_team_tournament_table extends Migration
{
    public function up()
    {
        $old = TeamTournaments::find()->all();

        foreach ($old as $one)
        {
            $new = new TeamTournament();
            $new->team_id = $one->id_team;
            $new->tournament_id = $one->id_tournament;
            $new->alias = $one->alias;
            $new->save(false);
        }
    }

    public function down()
    {
        echo "m170107_113418_populate_team_tournament_table cannot be reverted.\n";

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
