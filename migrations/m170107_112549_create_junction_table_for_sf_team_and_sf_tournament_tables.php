<?php

use yii\db\Migration;

/**
 * Handles the creation of table `sf_team_sf_tournament`.
 * Has foreign keys to the tables:
 *
 * - `sf_team`
 * - `sf_tournament`
 */
class m170107_112549_create_junction_table_for_sf_team_and_sf_tournament_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('sf_team_tournament', [
            'id' => $this->primaryKey(),
            'team_id' => $this->integer(),
            'tournament_id' => $this->integer(),
            'alias' => $this->string(),
        ]);

        // creates index for column `team_id`
        $this->createIndex(
            'idx-sf_team_tournament-team_id',
            'sf_team_tournament',
            'team_id'
        );

        // add foreign key for table `team`
        $this->addForeignKey(
            'fk-sf_team_tournament-team_id',
            'sf_team_tournament',
            'team_id',
            'sf_team',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // creates index for column `tournament_id`
        $this->createIndex(
            'idx-sf_team_tournament-tournament_id',
            'sf_team_tournament',
            'tournament_id'
        );

        // add foreign key for table `sf_tournament`
        $this->addForeignKey(
            'fk-sf_team_tournament-tournament_id',
            'sf_team_tournament',
            'tournament_id',
            'sf_tournament',
            'id',
            'RESTRICT',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `sf_team`
        $this->dropForeignKey(
            'fk-sf_team_tournament-team_id',
            'sf_team_tournament'
        );

        // drops index for column `sf_team_id`
        $this->dropIndex(
            'idx-sf_team_tournament-team_id',
            'sf_team_tournament'
        );

        // drops foreign key for table `sf_tournament`
        $this->dropForeignKey(
            'fk-sf_team_tournament-tournament_id',
            'sf_team_tournament'
        );

        // drops index for column `sf_tournament_id`
        $this->dropIndex(
            'idx-sf_team_tournament-tournament_id',
            'sf_team_tournament'
        );

        $this->dropTable('sf_team_tournament');
    }
}
