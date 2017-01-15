<?php

use yii\db\Migration;
use app\models\forecasts\Top3TeamsForecast;
use app\migratemodels\TournamentWinnerForecast;

class m170107_172348_populate_tournament_winner_forecast_table extends Migration
{
    public function safeUp()
    {
        $old = Top3TeamsForecast::find()->all();

        foreach ($old as $one)
        {
            $new = new TournamentWinnerForecast();
            $new->user_id = $one->id_user;
            $new->tournament_id = $one->id_tournament;
            $new->team_id = $one->team->id_team;
            $new->position = $one->forecasted_position;
            $new->date = $one->time;

            $new->save(false);
        }
    }

    public function down()
    {
        echo "m170107_172348_populate_tournament_winner_forecast_table cannot be reverted.\n";

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
