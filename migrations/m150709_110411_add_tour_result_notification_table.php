<?php

use yii\db\Schema;
use yii\db\Migration;

class m150709_110411_add_tour_result_notification_table extends Migration
{
    public function up()
    {
        $this->createTable('sf_tour_result_notification', [
            'id' => 'pk',
            'tournament' => Schema::TYPE_INTEGER,
            'tour' => Schema::TYPE_INTEGER,
            'date' => Schema::TYPE_INTEGER
        ]);
    }

    public function down()
    {
        $this->dropTable('sf_tour_result_notification');
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
