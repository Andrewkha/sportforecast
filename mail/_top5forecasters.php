<?php
use kartik\grid\GridView;
?>

<?= GridView::widget([
    'dataProvider' => $tourForecasts,
    'options' => [
        'style' => 'width: 30%'
    ],
    'summary' => '',
    'striped' => false,
    'responsive' => false,
    'toggleData' => false,
    'filterUrl' => false,
    'responsiveWrap' => false,
    'export' => false,
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
])
?>