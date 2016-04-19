<?php

use yii\db\Migration;
use app\models\users\UsersTournaments;
use app\models\result\Result;
use app\models\forecasts\Forecasts;
use yii\helpers\ArrayHelper;

class m160419_080020_add_points_column_into_user_tournaments_table extends Migration
{
    public function up()
    {
        $this->addColumn('{{%users_tournaments}}','points', $this->integer()->defaultValue(NULL));

        $tournaments = UsersTournaments::find()->all();

        foreach($tournaments as $one)
        {
            $games = ArrayHelper::getColumn(Result::find()->select('id_game')->where(['id_tournament' => $one->id_tournament])->all(), 'id_game');

            $points = Forecasts::find()
                ->select(['sum(points) as points'])
                ->joinWith('idUser')
                ->where(['in', 'id_game', $games])
                ->andWhere(['id_user' => $one->id_user])
                ->scalar();

            $one->points = $points;
            $one->save(false);
        }
    }

    public function down()
    {
        $this->dropColumn("{{%users_tournaments}}", 'points');
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
