<?php

use yii\db\Migration;
use app\models\games\Games;
use app\migratemodels\Game;
use app\models\result\Result;

class m170107_143425_populate_new_games_table extends Migration
{
    public function safeUp()
    {
        $old = Games::find()->all();

        foreach ($old as $one)
        {
            $new = new Game();
            $new->id = $one->id_game;
            $new->tournament_id = Result::findOne(['id_game' => $new->id])->id_tournament;
            $new->teamHome_id = $one->idTeamHome->id_team;
            $new->teamGuest_id = $one->idTeamGuest->id_team;
            $new->tour = $one->tour;
            $new->date = $one->date_time_game;
            $new->scoreHome = $one->score_home;
            $new->scoreGuest = $one->score_guest;

            $new->save(false);
        }
    }

    public function down()
    {
        echo "m170107_143425_populate_new_games_table cannot be reverted.\n";

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
