<?php

use yii\db\Migration;

class m170107_173627_new_reminders_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%forecast_reminders}}', [
            'user_id' => $this->integer(),
            'tournament_id' => $this->integer(),
            'tour' => $this->integer(2),
            'reminders' => $this->integer(1),
            'date' => $this->integer(),
            'PRIMARY KEY(user_id, tournament_id, tour)',
        ]);

        $this->createIndex('idx-forecast_reminders-user_id', '{{%forecast_reminders}}', 'user_id');
        $this->createIndex('idx-forecast_reminders-tournament_id', '{{%forecast_reminders}}', 'tournament_id');

        $this->addForeignKey('fk-forecast_reminders-user_id', '{{%forecast_reminders}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-forecast_reminders-tournament_id', '{{%forecast_reminders}}', 'tournament_id', '{{%tournament}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        echo "m170107_173627_new_reminders_table cannot be reverted.\n";

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
