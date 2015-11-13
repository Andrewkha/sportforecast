<?php

use app\components\grid\extendedGridView;
use yii\helpers\Html;

/**
 * @var $games
 * @var $trn
 * @var $tour
 * @var $forecasters
 */
?>

<p>Закончился <?=$tour?> тур <?=$trn->tournament_name?>, ознакомьтесь с его результатами</p>
Подробную информацию о турнире можно посомотреть на его <?=Html::a('странице', ['/tournaments/details', 'id' => $trn->id_tournament]);?>

<div class = 'row'>

    <?= extendedGridView::widget([
        'dataProvider' => $games,
        'caption' => 'Результаты тура',
        'showHeader' => false,
        'summary' => false,
        'toggleData' => false,
        'filterUrl' => false,
        'options' => [
            'class' => 'col-xs-12',
        ],
        'columns' => [

            [
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'options' => [
                    'class' => 'col-xs-2 reduceDateFont',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'content' => function($model) {

                    return date('d.m.y H:i', $model->date_time_game);
                }
            ],
            [
                'content' => function($model) {
                    return Html::img($model->idTeamHome->idTeam->fileUrl, ['width' => 30]).' '.$model->idTeamHome->idTeam->team_name;
                },
                'options' => [
                    'class' => 'col-xs-4'
                ],
                'contentOptions' => [
                    'align' => 'right',
                    'style' => 'vertical-align:middle',
                ],
            ],

            [
                'content' => function($model) {
                    return
                        "<strong>".$model->score_home.' - '.$model->score_guest."</strong>"
                        ;
                },
                'options' => [
                    'class' => 'col-xs-2'
                ],
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
            ],

            [
                'content' => function($model) {
                    return $model->idTeamGuest->idTeam->team_name.' '.Html::img($model->idTeamGuest->idTeam->fileUrl, ['width' => 30]);
                },
                'options' => [
                    'class' => 'col-xs-4'
                ],
                'contentOptions' => [
                    'align' => 'left',
                    'style' => 'vertical-align:middle',
                ],
            ],
        ]
    ]);?>

    <?= extendedGridView::widget([
        'dataProvider' => $forecasters,
        'caption' => 'Лучшие прогнозы тура',
        'summary' => false,
        'showHeader' => false,
        'toggleData' => false,
        'filterUrl' => false,
        'options' => [
            'class' => 'col-xs-6',
        ],
        'columns' => [
            [
                'header' => 'Пользователь',
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'options' => [
                    'class' => 'col-sm-10',
                ],
                'contentOptions' => [
                    'align' => 'left',
                    'style' => 'vertical-align:middle',
                ],
                'content' => function($model) {
                    return $model->idUser->username;
                }
            ],

            [
                'header' => 'Очки',
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'options' => [
                    'class' => 'col-sm-2',
                ],
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'content' => function($model) {
                    return $model->points;
                }
            ],
        ]
    ]);?>

</div>
