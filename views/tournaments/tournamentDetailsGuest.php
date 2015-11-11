<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\assets\ModalAsset;

ModalAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\tournaments\tournaments */

$this->title = $tournament->tournament_name;
$this->params['breadcrumbs'][] = ['label' => 'Турниры', 'url' => '@web/tournaments'];
$this->params['breadcrumbs'][] = $tournament->tournament_name;
?>

<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 tournaments-details">

        <h1 class = "text-center"><?= Html::encode($this->title) ?></h1>

        <hr>

        <div class = "row">
            <?= $this->render('_standings', [
                'teamParticipants' => $teamParticipants,
            ]);
            ?>

            <?= $this->render('_forecasters', [
                'forecasters' => $forecasters,
                'tournament' => $tournament,
            ]);
            ?>
        </div>

        <hr>

        <h1 class = "text-center">Игры по турам</h1>
        <div class = 'row'>
            <?= $this->render('_games', [
                'tour_list' => $tour_list,
                'tourGames' => $tourGames,
            ]);
            ?>
        </div>

    </div>
</div>
