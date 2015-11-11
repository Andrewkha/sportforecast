<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use app\models\users\Users;

?>

<?php
Modal::begin([
    'header' => '<h4>Прогноз по турам</h4>',
    'id' => 'mU',
    'size' => 'modal-sm',
]);
echo "<div id = 'modalUserContent'></div>";
Modal::end();
?>

<?= GridView::widget([
    'dataProvider' => $forecasters,
    'export' => false,
    'caption' => 'Лидеры прогноза',
    'summary' => false,
    'responsive' => false,
    'responsiveWrap' => false,
    'options' => [
        'class' => 'col-xs-12 col-md-5 col-lg-4 col-lg-offset-1',
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
            'attribute' => "name",
            'vAlign' => 'middle',
            'options' => [
                'class' => 'col-xs-9',
            ],
            'hAlign' => 'left',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'content' => function($model) use ($tournament){

                return Html::img(Users::getPath().'/'.$model['avatar'], ['height' => '30']).Html::button($model['name'], [
                    'value' => Url::to(['tournaments/user', 'user' => $model['id_user'], 'tournament' => $tournament->id_tournament]),
                    'class' => 'btn btn-link modalUser']);
            }
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

