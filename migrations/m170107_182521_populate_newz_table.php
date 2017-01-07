<?php

use yii\db\Migration;
use app\models\news\News;
use app\migratemodels\Newz;

class m170107_182521_populate_newz_table extends Migration
{
    public function up()
    {
        $old = News::find()->all();

        foreach ($old as $one)
        {
            $new = new Newz();
            $new->id = $one->id;
            $new->subject = $one->subject;
            $new->body = $one->body;
            $new->user_id = $one->author;
            $new->tournament_id = $one->id_tournament;
            $new->status = $one->archive;
            $new->date = $one->date;

            $new->save(false);
        }
    }

    public function down()
    {
        echo "m170107_182521_populate_newz_table cannot be reverted.\n";

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
