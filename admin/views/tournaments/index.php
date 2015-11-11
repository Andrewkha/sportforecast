<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\components\grid\extendedGridView;
use kartik\widgets\Alert;
use app\models\countries\Countries;

/* @var $this yii\web\View */
/* @var $searchModel app\models\tournaments\TournamentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Tournaments');
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;

if(Yii::$app->session->hasFlash('error')) {

    echo Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => 'Ошибка удаления записи',
        'icon' => 'glyphicon glyphicon-remove-sign',
        'body' => Yii::$app->session->getFlash('error'),
        'showSeparator' => true,
        'delay' => 2000
    ]);
}
?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 tournaments-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a(Yii::t('app', 'Create', [
                'modelClass' => 'Tournaments',
                ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="row">
            <?= extendedGridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'class' => 'col-xs-12 col-md-10 col-lg-8'
                ],
                'columns' => [
                    [
                        'attribute' => 'id_tournament',
                        'filter' => false,
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                    ],

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
                            'class' => 'col-xs-3'
                        ],
                        'content' => function($model) {
                            return Html::a($model->tournament_name, ['tournaments/update', 'id' => $model->id_tournament]);
                        },
                        'format' => 'url',
                    ],

                    [
                        'header' => 'Игры',
                        'filter' => false,
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-xs-2'
                        ],
                        'content' => function($model) {
                            return Html::a('Игры турнира', ['games/index', 'tournament' => $model->id_tournament]);
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
                            return Html::a($model->country0->country, ["countries/update", 'id' => $model->country]);
                        },
                        'format' => 'url',
                        'filter' => ArrayHelper::map(Countries::find()->orderBy('country', 'asc')->all(), 'id', 'country'),
                        'filterInputOptions' => [
                            'class' => 'form-control'
                        ],
                        'options' => [
                            'class' => 'col-xs-2'
                        ],
                    ],

                    [
                        'attribute' => 'num_tours',
                        'filter' => false,
                        'header' => 'Туры',
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                    ],

                    [
                        'attribute' => 'is_active',
                        'content' => function($model) {
                            return $model->status;
                        },
                        'options' => [
                            'class' => 'col-xs-2'
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
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{delete}',
                        'options' => [
                            'class' => 'hidden-xs'
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