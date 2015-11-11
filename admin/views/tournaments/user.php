<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 4/27/15
 * Time: 9:34 PM
 * To change this template use File | Settings | File Templates.
 */
use app\components\grid\extendedGridView;

?>
<h2 class = 'text-center'><?= $user->username;?></h2>

    <?= extendedGridView::widget([
        'dataProvider' => $forecast,
        'showPageSummary' => true,
        'summary' => false,
        'condensed' => true,
        'columns' => [

            [
                'attribute' => 'tour',
                'header' => 'Тур',
                'headerOptions' => [
                    'class' => 'text-center',
                ],
                'options' => [
                    'class' => 'col-xs-6',
                ],

                'hAlign' => 'center',
                'vAlign' => 'middle',

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
                'hAlign' => 'center',
                'vAlign' => 'middle',
                'pageSummary' => true,
                'pageSummaryOptions' => [
                    'align' => 'center',
                ]
            ],

        ]
    ]);
    ?>
