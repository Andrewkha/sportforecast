<?php

use yii\db\Migration;

class m160427_110948_add_winners_forecast_end_date_tournaments_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%tournaments}}','wfDueTo' , 'integer unsigned');
    }

    public function down()
    {
        $this->dropColumn('{{%tournaments}}', 'wfDueTo');
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
