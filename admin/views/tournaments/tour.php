<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 4/27/15
 * Time: 9:34 PM
 * To change this template use File | Settings | File Templates.
 */
use yii\grid\GridView;
?>

<?= GridView::widget([
    'dataProvider' => $forecast,
    'rowOptions' => function($model) {

        return ($model['status'] == 1)? ['class' => 'success'] : ['class' => 'danger'];
    },
    'columns' => [

        [
            'attribute' => 'time',
            'header' => 'Начало',
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
        ],

        [
            'attribute' => 'home_team',
            'header' => 'Хозяева',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-3',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
        ],

        [
            //'attribute' => 'home_score_forecast',
            'content' => function($model) {
                return $model['home_score_forecast'].' - '.$model['guest_score_forecast'];
            },
            'header' => 'Счет',
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
        ],

        [
            'attribute' => 'guest_team',
            'header' => 'Гости',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-3',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
        ],
    ]
]);