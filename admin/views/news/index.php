<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\StringHelper;
use kartik\widgets\Alert;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\news\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;

if(Yii::$app->session->hasFlash('error')) {

    echo Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => 'Ошибка',
        'icon' => 'glyphicon glyphicon-remove-sign',
        'body' => Yii::$app->session->getFlash('error'),
        'showSeparator' => true,
        'delay' => 4000
    ]);
}

if(Yii::$app->session->hasFlash('success')) {

    echo Alert::widget([
        'type' => Alert::TYPE_SUCCESS,
        'icon' => 'glyphicon glyphicon-remove-sign',
        'body' => Yii::$app->session->getFlash('success'),
        'showSeparator' => true,
        'delay' => 4000
    ]);
}
?>

<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 news-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <?php $form = ActiveForm::begin([
            'action' => ['news/archive'],
            'id' => 'news'
        ]);?>

        <?php ActiveForm::end();?>

        <div class="row">
            <?= GridView::widget([
                'export' => false,
                'showFooter' => true,
                'responsive' => false,
                'dataProvider' => $dataProvider,
                'options' => [
                    'class' => 'col-xs-12'
                ],
                'beforeFooter' => [
                    [
                        'columns'=>[
                            ['content'=>Html::submitButton('Применить', ['class' => 'btn btn-success', 'form' => 'news']), 'options'=>['colspan'=>7, 'style' => 'text-align: right']],
                        ],
                    ]
                ],
                'filterModel' => $searchModel,
                'columns' => [

                    [
                        'attribute' => 'id',
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'filter' => false,
                        'options' => [
                            'class' => 'col-xs-1'
                        ]
                    ],
                    [
                        'attribute' => 'subject',
                        'content' => function($model) {
                            return Html::a('<b>'.$model->subject.'</b>', ['news/update', 'id' => $model->id]);
                        },
                        'format' => 'html',
                        'contentOptions' => [
                            'style' => 'vertical-align:middle',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-xs-2'
                        ],
                    ],
                    [
                        'attribute' => 'body',
                        'format' => 'html',
                        'content' => function($model) {
                            return StringHelper::truncateWords($model->body, 10).Html::a('>>',['news/update', 'id' => $model->id]);
                        },
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-xs-4'
                        ],
                    ],
                    [
                        'attribute' => 'date',
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'content' => function($model) {
                            return date('d/m/y', $model->date);
                        },

                        'filter' => false,
                    ],

                    [
                        'attribute' => 'author',
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'content' => function($model) {
                            return $model->author0->username;
                        },
                        'filter' => $author,
                    ],

                    [
                        'attribute' => 'id_tournament',
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'content' => function($model) {
                            return ($model->id_tournament == 0)? 'Новости сайта' : $model->tournament->tournament_name;
                        },
                        'filter' => $tournament,
                    ],

                    [
                        'attribute' => 'archive',
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'content' => function($model) use ($form){

                            return Html::hiddenInput("News[$model->id][archive]", 0, [
                                'form' => 'news'
                            ]).$form->field($model, "[$model->id]archive")->checkbox(['uncheck' => null, 'label' => '', 'form' => 'news']);
                        },
                        'filter' => $archive,
                    ],

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{delete}',
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>