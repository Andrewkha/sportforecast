<?php

use yii\db\Migration;

class m170115_180359_drop_old_tables extends Migration
{
    public function safeUp()
    {
        $this->dropTable('{{%news}}');
        //$this->dropTable('{{%result}}');
        $this->dropTable('{{%forecasts}}');
        $this->dropTable('{{%users_tournaments}}');
        $this->dropTable('{{%games}}');
        $this->dropTable('{{%top_3_teams_forecast}}');
        $this->dropTable('{{%reminders_user_tournament_tour}}');
        $this->dropTable('{{%team_tournaments}}');
        $this->dropTable('{{%tour_result_notification}}');
        $this->dropTable('{{%tournaments}}');
        $this->dropTable('{{%teams}}');
        $this->dropTable('{{%countries}}');
        $this->dropTable('{{%users}}');
    }

    public function down()
    {
        echo "m170115_180359_drop_old_tables cannot be reverted.\n";

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
