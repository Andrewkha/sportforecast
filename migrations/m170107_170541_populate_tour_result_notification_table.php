<?php

use yii\db\Migration;
use app\models\tournaments\TourResultNotifications;
use app\migratemodels\TourResultNotification;

class m170107_170541_populate_tour_result_notification_table extends Migration
{
    public function safeUp()
    {
        $old = TourResultNotifications::find()->all();

        foreach ($old as $one)
        {
            $new = new TourResultNotification();
            $new->tournament_id = $one->tournament;
            $new->tour = $one->tour;
            $new->date = $one->date;

            $new->save(false);
        }
    }

    public function down()
    {
        echo "m170107_170541_populate_tour_result_notification_table cannot be reverted.\n";

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
