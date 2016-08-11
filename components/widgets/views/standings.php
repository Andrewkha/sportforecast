<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\teams\Teams;

?>

<?= GridView::widget([
    'dataProvider' => $teamParticipants,
    'summary' => false,
    'caption' => 'Турнирная таблица',
    'responsive' => false,
    'responsiveWrap' => false,
    'export' => false,
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
                return Html::img(Teams::getPath().'/'.$model['team_logo'], ['width' => 30]).' '.Html::a($model['team_name'], ["games", 'id' => $model['participant']]);
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
]);?>