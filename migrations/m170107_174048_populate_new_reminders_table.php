<?php

use yii\db\Migration;
use app\models\reminders\Reminders;
use app\migratemodels\ForecastReminders;

class m170107_174048_populate_new_reminders_table extends Migration
{
    public function safeUp()
    {
        $old = Reminders::find()->all();

        foreach ($old as $one)
        {
            $new = new ForecastReminders();
            $new->user_id = $one->user;
            $new->tournament_id = $one->tournament;
            $new->tour = $one->tour;
            if ($one->reminders == 2) {
                $new2 = new ForecastReminders();
                $new2->user_id = $one->user;
                $new2->tournament_id = $one->tournament;
                $new2->tour = $one->tour;
                $new2->date = $one->date;
                $new2->save(false);
            }
            $new->date = $one->date;

            $new->save(false);
        }
    }

    public function down()
    {
        echo "m170107_174048_populate_new_reminders_table cannot be reverted.\n";

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
