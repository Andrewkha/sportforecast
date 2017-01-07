<?php

use yii\db\Migration;

class m170107_123328_new_games_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%game}}', [
            'id' => $this->primaryKey(),
            'tournament_id' => $this->integer(),
            'teamHome_id' => $this->integer()->notNull(),
            'teamGuest_id' => $this->integer()->notNull(),
            'tour' => $this->integer(2)->notNull(),
            'date' => $this->integer(),
            'scoreHome' => $this->integer(2)->Null(),
            'scoreGuest' => $this->integer(2)->Null(),
        ]);

        $this->createIndex('idx-game-tournament_id', '{{%game}}','tournament_id');
        $this->createIndex('idx-game-teamHome_id', '{{%game}}','teamHome_id');
        $this->createIndex('idx-game-teamGuest_id', '{{%game}}','teamGuest_id');

        $this->addForeignKey('fk-game-tournament_id', '{{%game}}', 'tournament_id', '{{%tournament}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-game-teamHome_id', '{{%game}}', 'teamHome_id', '{{%team}}', 'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk-game-teamGuest_id', '{{%game}}', 'teamGuest_id', '{{%team}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        echo "m170107_123328_new_games_table cannot be reverted.\n";

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
