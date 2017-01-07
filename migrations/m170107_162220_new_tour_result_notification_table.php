<?php

use yii\db\Migration;

class m170107_162220_new_tour_result_notification_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tourResultNotification}}', [
            'tournament_id' => $this->integer(),
            'tour' => $this->integer(2),
            'date' => $this->integer(),
            'PRIMARY KEY(tournament_id, tour)',
        ]);

        $this->createIndex('idx-tourResultNotification-tournament_id', '{{%tourResultNotification}}', 'tournament_id');

        $this->addForeignKey('fk-tourResultNotification-tournament_id', '{{%tourResultNotification}}', 'tournament_id', '{{%tournament}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        echo "m170107_162220_new_tour_result_notification_table cannot be reverted.\n";

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
