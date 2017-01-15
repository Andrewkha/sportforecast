<?php

use yii\db\Migration;
use app\models\forecasts\Forecasts;
use app\migratemodels\Forecast;

class m170107_175919_populate_new_forecast_table extends Migration
{
    public function safeUp()
    {
        $old = Forecasts::find()->all();

        foreach ($old as $one)
        {
            $new = new Forecast();
            $new->user_id = $one->id_user;
            $new->game_id = $one->id_game;
            $new->fscoreHome = $one->fscore_home;
            $new->fscoreGuest = $one->fscore_guest;
            $new->date = $one->date;

            $new->save(false);
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
