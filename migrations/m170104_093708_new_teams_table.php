<?php

use yii\db\Migration;

class m170104_093708_new_teams_table extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%team}}', [
            'id' => $this->primaryKey(),
            'team' => $this->string('50')->notNull(),
            'country_id' => $this->integer()->notNull(),
            'logo' => $this->string(),
        ]);

        $this->createIndex('idx-team-country_id', '{{%team}}','country_id');

        $this->addForeignKey('fk-team-country_id', '{{%team}}', 'country_id', '{{%country}}', 'id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {

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
