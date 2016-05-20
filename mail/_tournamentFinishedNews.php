<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 11/11/2015
 * Time: 4:10 PM
 */

use app\components\grid\extendedGridView;
use yii\helpers\Html;
?>
<?= Html::tag('p', "Закончился турнир $trn->tournament_name. Пожалуйста, ознакомьтесь с его результатами");?>

<?= "Подробную информацию о турнире можно посомотреть на его ".Html::a('странице', ['/tournaments/details', 'id' => $trn->id_tournament]);?>

<br>

<div class = 'row'>

    <?= extendedGridView::widget([
        'dataProvider' => $forecasters,
        'caption' => 'Победители прогноза',
        'summary' => false,
        'showHeader' => false,
        'toggleData' => false,
        'filterUrl' => false,
        'options' => [
            'class' => 'col-xs-12 col-md-5 col-lg-5',
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'options' => [
                    'class' => 'col-xs-1',
                ],
                'header' => 'Место',
            ],

            [
                'header' => 'Пользователь',
                'vAlign' => 'middle',
                'options' => [
                    'class' => 'col-xs-9',
                ],
                'hAlign' => 'left',
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'attribute' => 'idUser.username'
            ],

            [
                'header' => 'Очки',
                'attribute' => "points",
                'vAlign' => 'middle',
                'options' => [
                    'class' => 'col-xs-1',
                ],
                'hAlign' => 'center',
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
            ],
        ]
    ]);?>

    <?= extendedGridView::widget([
        'dataProvider' => $standings,
        'options' => [
            'class' => 'col-xs-12 col-md-5 col-md-offset-1 col-lg-6 col-lg-offset-1'
        ],
        'summary' => false,
        'showHeader' => false,
        'toggleData' => false,
        'filterUrl' => false,
        'caption' => 'Турнирная таблица',
        'columns' =>  [
            [
                'header' => 'Место',
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'options' => [
                    'class' => 'col-xs-1'
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
            ],

            [
                'header' => 'Команда',
                'content' => function($model) {
                    return $model['team_name'];
                },
                'contentOptions' => [
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'options' => [
                    'class' => 'col-xs-9'
                ],
            ],

            [
                'header' => 'Игры',
                'attribute' => 'games_played',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'options' => [
                    'class' => 'col-xs-1'
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'content' => function ($model) {
                    return (isset($model['games_played']))? $model['games_played'] : 0;
                }
            ],

            [
                'header' => 'Очки',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'options' => [
                    'class' => 'col-xs-1'
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'content' => function ($model) {
                    return (isset($model['pts']))? $model['pts'] : 0;
                }
            ],
        ]
    ]); ?>

</div>