<?php

use yii\db\Migration;
use app\models\teams\Teams;
use app\migratemodels\Team;

class m170104_094856_populate_new_teams_table extends Migration
{
    public function up()
    {
        $oldTeams = Teams::find()->all();

        foreach ($oldTeams as $one)
        {
            $newTeam = new Team();
            $newTeam->id = $one->id_team;
            $newTeam->team = $one->team_name;
            $newTeam->country_id = $one->country;
            $newTeam->logo = $one->team_logo;
            $newTeam->save(false);
        }
    }

    public function down()
    {
        $this->dropTable('{{%team}}');
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
