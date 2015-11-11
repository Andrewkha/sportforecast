<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Alert;

/* @var $this yii\web\View */
/* @var $searchModel app\models\countries\CountriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Страны');
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
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 countries-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a(Yii::t('app', 'Создать', [
                'modelClass' => 'Countries',
            ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="row">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'class' => 'col-xs-12 col-sm-10 col-md-8 col-lg-4'
                ],
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'options' => [
                            'class' => 'col-xs-2',
                        ],
                        'contentOptions' => [
                            'align' => 'center',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                    ],

                    [
                        'attribute' => 'id',
                        'options' => [
                            'class' => 'col-xs-2'
                        ],
                        'contentOptions' => [
                            'align' => 'center',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],

                    ],

                    [
                        'attribute' => 'country',
                        'value' => function($model) {
                            return Html::a($model->country, ['countries/update', 'id' => $model->id]);
                        },
                        'options' => [
                            'class' => 'col-xs-6',
                        ],
                        'format' => 'raw',
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                    ],

                    [
                        'header' => 'Удалить',
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{delete}',
                        'options' => [
                            'class' => 'col-xs-2'
                        ],
                        'contentOptions' => [
                            'align' => 'center',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>