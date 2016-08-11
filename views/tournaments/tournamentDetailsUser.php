<?php

use yii\helpers\Html;
use kartik\widgets\Growl;
use app\assets\ModalAsset;
use app\components\widgets\Standings;
use yii\bootstrap\Collapse;

/**
 * @var $tournament \app\models\tournaments\Tournaments
 * @var $teamParticipants
 * @var $forecasters
 * @var $tour_list
 * @var $tourGames
 */

ModalAsset::register($this);
/* @var $this yii\web\View */

$this->title = $tournament->tournament_name;
$this->params['breadcrumbs'][] = ['label' => 'Турниры', 'url' => '/tournaments'];
$this->params['breadcrumbs'][] = $tournament->tournament_name;
?>

<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 tournaments-details">

        <?php
        if(Yii::$app->session->hasFlash('success')) {

            echo Growl::widget([
                'type' => Growl::TYPE_SUCCESS,
                'icon' => 'glyphicon glyphicon-ok-sign',
                'body' => Yii::$app->session->getFlash('success'),
                'showSeparator' => true,
                'pluginOptions' => [
                    'placement' => [
                        'align' => 'left'
                    ]
                ]
            ]);
        }
        ?>
        <?php
        if(Yii::$app->session->hasFlash('error')) {

            echo Growl::widget([
                'type' => Growl::TYPE_DANGER,
                'icon' => 'glyphicon glyphicon-flag',
                'body' => Yii::$app->session->getFlash('error'),
                'showSeparator' => true,
                'pluginOptions' => [
                    'placement' => [
                        'align' => 'left'
                    ]
                ]
            ]);
        }
        ?>

        <h1 class = "text-center"><?= Html::encode($this->title) ?></h1>

        <hr>

        <div class = "row">

            <div class = 'col-xs-12 col-md-5 col-md-offset-1 col-lg-5'>
                <?= Standings::widget(['standings' => $teamParticipants]);?>
            </div>

            <? $collapseContent = Standings::widget(['standings' => $forecastedStandings]) ;?>

            <div class = 'col-xs-12 col-md-5 col-md-offset-1 col-lg-5'>
                <?= Collapse::widget([
                    'encodeLabels' => false,
                    'items' => [
                        [
                            'label' => 'Если сбылись все Ваши прогнозы...',
                            'content' => $collapseContent,
                        ],
                    ]
                ]);?>
            </div>

            <?= $this->render('_winners', [
                'winners' => $winners,
                'tournament' => $tournament,
                'additionalPoints' => $additionalPoints,
                'totalAdditionalPoints' => $totalAdditionalPoints
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
            <?= $this->render('_gamesUser', [
                'tour_list' => $tour_list,
                'tourGames' => $tourGames,
                'tournament' => $tournament,
            ]);
            ?>
        </div>

    </div>
</div>
