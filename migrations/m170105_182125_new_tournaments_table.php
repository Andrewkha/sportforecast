<?php

use yii\db\Migration;

class m170105_182125_new_tournaments_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tournament}}', [
            'id' => $this->primaryKey(),
            'tournament' => $this->string('150')->notNull(),
            'country_id' => $this->integer()->notNull(),
            'logo' => $this->string(),
            'type' => $this->integer(1)->Null(),
            'tours' => $this->integer(2)->Null(),
            'status' => $this->integer(1)->Null(),
            'starts' => $this->integer(),
            'autoprocess' => $this->integer(1),
            'autoprocessURL' => $this->string(),
            'winnersForecastDue' => $this->integer(),
        ]);

        $this->createIndex('idx-tournament-country_id', '{{%tournament}}','country_id');
        $this->createIndex('idx-tournament-status', '{{%tournament}}','status');

        $this->addForeignKey('fk-tournament-country_id', '{{%tournament}}', 'country_id', '{{%country}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropTable('{{%tournament}}');
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
