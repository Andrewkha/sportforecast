<?php

use yii\db\Migration;

/**
 * Handles the creation of table `sf_user_sf_tournament`.
 * Has foreign keys to the tables:
 *
 * - `sf_user`
 * - `sf_tournament`
 */
class m170107_161031_create_junction_table_for_sf_user_and_sf_tournament_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('sf_user_tournament', [
            'user_id' => $this->integer(),
            'tournament_id' => $this->integer(),
            'notification' => $this->integer(1),
            'PRIMARY KEY(user_id, tournament_id)',
        ]);

        // creates index for column `sf_user_id`
        $this->createIndex(
            'idx-sf_user_tournament-user_id',
            'sf_user_tournament',
            'user_id'
        );

        // add foreign key for table `sf_user`
        $this->addForeignKey(
            'fk-sf_user_tournament-user_id',
            'sf_user_tournament',
            'user_id',
            'sf_user',
            'id',
            'CASCADE',
            'CASCADE'
        );

        // creates index for column `sf_tournament_id`
        $this->createIndex(
            'idx-sf_user_tournament-tournament_id',
            'sf_user_tournament',
            'tournament_id'
        );

        // add foreign key for table `sf_tournament`
        $this->addForeignKey(
            'fk-sf_user_tournament-tournament_id',
            'sf_user_tournament',
            'tournament_id',
            'sf_tournament',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `sf_user`
        $this->dropForeignKey(
            'fk-sf_user_sf_tournament-sf_user_id',
            'sf_user_sf_tournament'
        );

        // drops index for column `sf_user_id`
        $this->dropIndex(
            'idx-sf_user_sf_tournament-sf_user_id',
            'sf_user_sf_tournament'
        );

        // drops foreign key for table `sf_tournament`
        $this->dropForeignKey(
            'fk-sf_user_sf_tournament-sf_tournament_id',
            'sf_user_sf_tournament'
        );

        // drops index for column `sf_tournament_id`
        $this->dropIndex(
            'idx-sf_user_sf_tournament-sf_tournament_id',
            'sf_user_sf_tournament'
        );

        $this->dropTable('sf_user_sf_tournament');
    }
}
