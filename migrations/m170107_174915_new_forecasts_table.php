<?php

use yii\db\Migration;

class m170107_174915_new_forecasts_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%forecast}}', [
            'user_id' => $this->integer(),
            'game_id' => $this->integer(),
            'fscoreHome' => $this->integer(2)->Null(),
            'fscoreGuest' => $this->integer(2)->Null(),
            'date' => $this->integer(),
            'PRIMARY KEY(user_id, game_id)',
        ]);

        $this->createIndex('idx-forecast-user_id', '{{%forecast}}', 'user_id');
        $this->createIndex('idx-forecast-game_id', '{{%forecast}}', 'game_id');

        $this->addForeignKey('fk-forecast-user_id', '{{%forecast}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-forecast-game_id', '{{%forecast}}', 'game_id', '{{%game}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%forecast}}');
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
