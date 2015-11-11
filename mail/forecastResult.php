<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 4/23/15
 * Time: 6:14 PM
 * To change this template use File | Settings | File Templates.
 */
use kartik\grid\GridView;
use yii\helpers\Html;

?>
<h4>Уважаемый <?= $user->username;?>!</h4>
<p>
    <?= "Окончен $tour тур в турнире $tournament. Ознакомьтесь с результатами Вашего прогноза";?>
</p>

</br>

<?= GridView::widget([
    'dataProvider' => $content,
    'options' => [
        'style' => 'width: 80%'
    ],
    'summary' => '',
    'striped' => false,
    'filterUrl' => false,
    'toggleData' => false,
    'export' => false,
    'showPageSummary' => true,
    'rowOptions' => function($model) {

        if($model['status'] === false)
            return ['class' => 'danger'];

        if($model['fpoints'] == 3)
            return ['class' => 'success'];

        if($model['fpoints'] == 2)
            return ['class' => 'warning'];

        if($model['fpoints'] == 1)
            return ['class' => 'info'];
    },
    'columns' => [

        [
            'attribute' => 'dtime',
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
            'content' => function($model) {
                return date('d.m.y H:i', $model['dtime']);
            },
        ],

        [
            'header' => 'Хозяева',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-4',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
            'pageSummary' => '<strong>Всего в туре:</strong>',
            'content' => function($model) {
                return $model['home_team'];
            }
        ],

        [
            'header' => 'Счет <br><small><u>Прогноз</u></small>',
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
            'pageSummary' => false,
            'content' => function($model) {
                return '<strong>'.$model['home_score'].' - '.$model['guest_score'].'</strong> <br>'.
                    '<strong><small><u>'.$model['fscore_home'].' - '.$model['fscore_guest'].'</u></small></strong>';
            }
        ],

        [
            'header' => 'Гости',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-4',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
            'pageSummary' => false,
            'content' => function($model) {
                return $model['guest_team'];
            }
        ],

        [
            'header' => 'Очки',
            'attribute' => 'fpoints',
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
                return '<strong>'.$model['fpoints'].'</strong>';
            },
            'pageSummary' => true,
            'pageSummaryOptions' => [
                'align' => 'center',
                'prepend' => '<strong>',
                'append' => '</strong>',
            ]
        ],

    ]
]);
?>
<br>

<h3>Лучшие 5 прогнозистов тура</h3>
<?= $this->render('_top5forecasters', [
    'tourForecasts' => $tourForecasts,
]) ?>

<br>

<h3>Всего в турнире</h3>
<?= $this->render('standings', [
    'standings' => $standings,
]) ?>
