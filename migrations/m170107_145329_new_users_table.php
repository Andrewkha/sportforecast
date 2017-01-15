<?php

use yii\db\Migration;

class m170107_145329_new_users_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(),
            'password' => $this->string(),
            'email' => $this->string(),
            'firstName' => $this->string(50),
            'lastName' => $this->string(50),
            'avatar' => $this->string(),
            'forgottenPasswordCode' => $this->string(),
            'activationString' => $this->string(),
            'auth_key' => $this->string(32),
            'userStatus' => $this->integer(1),
            'notificationsStatus' => $this->integer(),

            'created_on' => $this->integer(),
            'updated_on' => $this->integer(),
            'lastLogin' => $this->integer(),
        ]);

        $this->createIndex('idx-user-username', '{{%user}}', 'username');
    }

    public function down()
    {
        echo "m170107_145329_new_users_table cannot be reverted.\n";

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
