<?php

use yii\db\Migration;

class m170107_180835_new_news_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%newz}}', [
            'id' => $this->primaryKey(),
            'subject' => $this->string(1024)->notNull(),
            'body' => $this->text()->notNull(),
            'user_id' => $this->integer(),
            'tournament_id' => $this->integer(),
            'date' => $this->integer(),
            'status' => $this->integer(1),
        ]);

        $this->createIndex('idx-newz-user_id', '{{%newz}}', 'user_id');
        $this->createIndex('idx-newz-tournament_id', '{{%newz}}', 'tournament_id');

        $this->addForeignKey('fk-newz-user_id', '{{%newz}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        echo "m170107_180835_new_news_table cannot be reverted.\n";

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
