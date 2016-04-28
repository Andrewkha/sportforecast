<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 4/28/2016
 * Time: 5:58 PM
 */
use app\components\grid\extendedGridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<h4>Уважаемый <?= $user->idUser->username;?>!</h4>
<p><?=$message;?></p>
<br>

<h5>Полная таблица результатов прогноза</h5>

<?= extendedGridView::widget([
    'dataProvider' => $standings,
    'caption' => 'Победители прогноза',
    'summary' => false,
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
            'content' => function($model){
                return $model->totalPoints;
            },
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
])
;?>
<br>
<p>Полная информация о турнире доступна на его <?= Html::a('странице', Url::to(Yii::$app->params['url']."/tournaments/$tournament->id_tournament", true));?></p>
