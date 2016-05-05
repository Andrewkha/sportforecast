<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 4/27/15
 * Time: 9:34 PM
 * To change this template use File | Settings | File Templates.
 */
use app\components\grid\extendedGridView;
use yii\bootstrap\Html;

?>
<h2 class = 'text-center'><?= $user->username;?></h2>

<?= Html::beginTag('h4', ['class' => 'text-center'])?>
    Прогноз - призеры турнира
<?= Html::endTag('h4');?>

<?php if(!empty($winnersForecast->allModels)) :?>

    <?= extendedGridView::widget([
        'dataProvider' => $winnersForecast,
        'showPageSummary' => false,
        'summary' => false,
        'condensed' => true,
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
                'attribute' => 'team.idTeam.team_name',
                'header' => 'Команда',
                'headerOptions' => [
                    'class' => 'text-center',
                ],
                'options' => [
                    'class' => 'col-xs-11',
                ],

                'hAlign' => 'center',
                'vAlign' => 'middle',

                'pageSummary' => 'Всего'
            ],
        ]
    ]);
    ?>

<?php else :?>
    <?= Html::beginTag('h5', ['class' => 'text-center'])?>
    Прогноз не был сделан
    <?= Html::endTag('h5');?>
<?php endif;?>

<?php if($isFinished && !empty($winnersForecast->allModels)) :?>
    <p><?=$winnersForecastDetails;?></p>
    <p><b>Общее количество дополнительных очков: <?= $totalAdditionalPoints;?></b></p>
<?php endif;?>

<hr>
<?= Html::beginTag('h4', ['class' => 'text-center'])?>
    Очки по турам
<?= Html::endTag('h4');?>

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
