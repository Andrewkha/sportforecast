<?php

use yii\helpers\Html;
use app\components\grid\extendedGridView;
use app\models\forecasts\Forecasts;

/* @var $this yii\web\View */
/* @var $searchModel app\models\tournaments\TournamentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Турниры');

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10">
        <h2>Статистика по всем турнирам</h2>

        <div class="row">
            <?= extendedGridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bordered' => false,
                'options' => [
                    'class' => 'col-xs-12 col-md-10 col-lg-8'
                ],
                'resizableColumns' => false,
                'summary' => false,
                'columns' => [

                    [
                        'attribute' => 'tournament_name',
                        'filter' => false,
                        'contentOptions' => [
                            'style' => 'vertical-align:middle',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-lg-4'
                        ],
                        'content' => function($model) {
                            return Html::a($model->tournament_name, ['tournaments/details', 'id' => $model->id_tournament]);
                        },
                        'format' => 'url',
                    ],

                    [
                        'attribute' => 'country',
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'content' => function($model) {
                            return $model->country0->country;
                        },
                        'filter' => $countries,
                        'filterInputOptions' => [
                            'class' => 'form-control'
                        ],
                        'options' => [
                            'class' => 'col-lg-2'
                        ],
                    ],

                    [
                        'attribute' => 'is_active',
                        'content' => function($model) {
                            return $model->status;
                        },
                        'options' => [
                            'class' => 'col-lg-2'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'filter' => ['0' => 'Не начался','1' => 'Проходит','2' => 'Закончен'],
                    ],

                    [
                        'header' => 'Лидер прогноза',
                        'filter' => false,
                        'contentOptions' => [
                            'style' => 'vertical-align:middle',
                            'align' => 'center'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-lg-2'
                        ],
                        'content' => function($model) {
                            return \app\models\users\UsersTournaments::find()->leader($model->id_tournament)->one()->idUser->username;
                        },
                    ],

                    [
                        'header' => 'Очки лидера',
                        'filter' => false,
                        'contentOptions' => [
                            'style' => 'vertical-align:middle',
                            'align' => 'center'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-lg-2'
                        ],
                        'content' => function($model) {
                            return \app\models\users\UsersTournaments::find()->leader($model->id_tournament)->one()->points;
                        },
                    ],


                    [
                        'attribute' => 'startsOn',
                        'content' => function($model) {
                            return ($model->startsOn == NULL) ? '-' : date('d.m.Y',$model->startsOn);
                        },
                        'visible' => false,
                        'options' => [
                            'class' => 'col-lg-1'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                    ],

                ],
            ]); ?>
        </div>
    </div>
</div>
