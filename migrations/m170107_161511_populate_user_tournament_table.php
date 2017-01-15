<?php

use yii\db\Migration;
use app\models\users\UsersTournaments;
use app\migratemodels\UserTournament;

class m170107_161511_populate_user_tournament_table extends Migration
{
    public function safeUp()
    {
        $old = UsersTournaments::find()->all();

        foreach ($old as $one)
        {
            $new = new UserTournament();
            $new->user_id = $one->id_user;
            $new->tournament_id = $one->id_tournament;
            $new->notification = $one->notification;

            $new->save(false);
        }
    }

    public function down()
    {
        echo "m170107_161511_populate_user_tournament_table cannot be reverted.\n";

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
