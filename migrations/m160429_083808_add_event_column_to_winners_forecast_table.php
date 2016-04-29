<?php

use yii\db\Migration;

class m160429_083808_add_event_column_to_winners_forecast_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%top_3_teams_forecast}}', 'event', 'smallint');
    }

    public function down()
    {
        $this->dropColumn('{{%top_3_teams_forecast}}', 'event');
    }
}
