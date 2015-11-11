<?php

//use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Alert;
use kartik\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\teams\TeamsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Teams');
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
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 teams-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
        <?= Html::a(Yii::t('app', 'Create', [
            'modelClass' => 'Teams',
            ]), ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="row">
            <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pager' => [
                'firstPageLabel' => Html::icon('fast-backward'),
                'prevPageLabel' => Html::icon('backward'),
                'nextPageLabel' => Html::icon('forward'),
                'lastPageLabel' => Html::icon('fast-forward'),
            ],
            'options' => [
                'class' => 'col-xs-12 col-md-10 col-lg-7'
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
                        'class' => 'col-xs-1'
                    ],
                ],

                [
                    'attribute' => 'id_team',
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
                    'attribute' => 'team_name',
                    'options' => [
                        'class' => 'col-xs-4',
                    ],
                    'contentOptions' => [
                        'style' => 'vertical-align:middle',
                    ],
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'value' => function($model) {
                        return Html::a($model->team_name, ['teams/update', 'id' => $model->id_team]);
                    },
                    'format' => 'raw',
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
                    'value' => function($model) {
                        return Html::a($model->country0->country, ['countries/update', 'id' => $model->country]);
                    },
                    'format' => 'raw',
                    'filter' => \app\models\countries\Countries::getCountriesArray(),
                    'filterInputOptions' => [
                        'class' => 'form-control'
                    ],
                    'options' => [
                        'class' => 'col-xs-3'
                    ],
                ],
                [
                    'attribute' => 'team_logo',
                    'options' => [
                        'class' => 'col-xs-2'
                    ],
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'filter' => false,

                    'format' => 'raw',
                    'value' => function($model) {
                        return Html::img($model->fileUrl, ['height' => '50', 'width' => '50']);
                    },

                    'contentOptions' => [
                        'align' => 'center',
                    ],
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Удалить',
                    'template' => '{delete}',
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
            ],
        ]); ?>
        </div>

    </div>
</div>