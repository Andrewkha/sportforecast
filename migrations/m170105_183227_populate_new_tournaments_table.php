<?php

use yii\db\Migration;
use app\models\tournaments\Tournaments;
use app\migratemodels\Tournament;

class m170105_183227_populate_new_tournaments_table extends Migration
{
    public function safeUp()
    {
        $oldTournaments = Tournaments::find()->all();

        foreach ($oldTournaments as $one)
        {
            $newTournament = new Tournament();
            $newTournament->id = $one->id_tournament;
            $newTournament->tournament = $one->tournament_name;
            $newTournament->country_id = $one->country;
            $newTournament->tours = $one->num_tours;
            $newTournament->type = $one->id_tournament === 16 ? 0 : 1;
            $newTournament->status = $one->is_active;
            $newTournament->starts = $one->startsOn;
            $newTournament->autoprocess = $one->enableAutoprocess;
            $newTournament->autoprocessURL = $one->autoProcessURL;
            $newTournament->winnersForecastDue = $one->wfDueTo;
            $newTournament->logo = 'nologo.jpeg';
            $newTournament->save(false);
        }
    }

    public function down()
    {

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
