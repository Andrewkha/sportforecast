<?php

use yii\db\Migration;

class m170107_171151_new_tournaments_winners_forecast_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%tournament_winner_forecast}}', [
            'user_id' => $this->integer(),
            'tournament_id' => $this->integer(),
            'team_id' => $this->integer(),
            'position' => $this->integer(1),
            'date' => $this->integer(),
            'PRIMARY KEY(user_id, tournament_id, team_id)',
        ]);

        $this->createIndex('idx-tournament_winner_forecast-user_id', '{{%tournament_winner_forecast}}', 'user_id');
        $this->createIndex('idx-tournament_winner_forecast-tournament_id', '{{%tournament_winner_forecast}}', 'tournament_id');
        $this->createIndex('idx-tournament_winner_forecast-team_id', '{{%tournament_winner_forecast}}', 'team_id');

        $this->addForeignKey('fk-tournament_winner_forecast-user_id', '{{%tournament_winner_forecast}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-tournament_winner_forecast-tournament_id', '{{%tournament_winner_forecast}}', 'tournament_id', '{{%tournament}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk-tournament_winner_forecast-team_id', '{{%tournament_winner_forecast}}', 'team_id', '{{%team}}', 'id', 'CASCADE', 'CASCADE');

    }

    public function down()
    {
        $this->dropTable('{{%tournament_winner_forecast}}');
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
