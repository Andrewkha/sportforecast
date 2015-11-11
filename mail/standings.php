<?php
use kartik\grid\GridView;
?>

<?= GridView::widget([
    'dataProvider' => $standings,
    'options' => [
        'style' => 'width: 50%'
    ],
    'summary' => '',
    'striped' => false,
    'responsive' => false,
    'toggleData' => false,
    'filterUrl' => false,
    'export' => false,
    'columns' => [

        [
            'attribute' => 'userPosition',
            'header' => 'Место',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-1',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
            'content' => function($model) {
                return $model['userPosition'];
            }
        ],

        [
            'header' => 'Очки',
            'attribute' => 'userPoints',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-1',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
            'content' => function($model) {
                return $model['userPoints'];
            }
        ],

        [
            'header' => 'Всего участников',
            'attribute' => 'count',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-1',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
            'content' => function($model) {
                return $model['count'];
            }
        ],

        [
            'header' => 'Лидер',
            'attribute' => 'leader',
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
                return $model['leader'];
            }
        ],
        [
            'header' => 'Очки лидера',
            'attribute' => 'leaderPoints',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-1',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
            'content' => function($model) {
                return $model['leaderPoints'];
            }
        ],
    ]
])
?>