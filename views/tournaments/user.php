<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 4/27/15
 * Time: 9:34 PM
 * To change this template use File | Settings | File Templates.
 */
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
?>
<h2 class = 'text-center'><?= $user->username;?></h2>
<?= GridView::widget([
    'dataProvider' => $forecast,
    'showPageSummary' => true,
    'summary' => false,
    'responsive' => false,
    'responsiveWrap' => false,
    'condensed' => true,
    'export' => false,
    'columns' => [

        [
            'attribute' => 'tour',
            'header' => 'Тур',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-xs-6',
            ],
            'contentOptions' => [
                'class' => 'text-center',
                'style' => 'vertical-align:middle',
            ],
            'pageSummary' => 'Всего'
        ],

        [
            'attribute' => 'points',
            'header' => 'Очки',
            'headerOptions' => [
                'class' => 'text-center',
            ],
            'options' => [
                'class' => 'col-xs-6',
            ],
            'contentOptions' => [
                'class' => 'text-center',
                'style' => 'vertical-align:middle',
            ],
            'pageSummary' => true,
            'pageSummaryOptions' => [
                'align' => 'center',
            ]
        ],

    ]
]);
?>
