<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\users\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Управление пользователями';
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 users-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => false,
        'responsiveWrap' => false,
        'options' => [
            'class' => 'col-lg-10'
        ],
        'export' => false,
        'columns' => [

            [
                'attribute' => 'id',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'filter' => false,
            ],
            [
                'attribute' => 'username',
                'options' => [
                    'class' => 'col-xs-'
                ],
                'contentOptions' => [
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'value' => function($model) {
                    return Html::a($model->username, ['users/update', 'id' => $model->id]);
                },
                'format' => 'html',
            ],

            [
                'attribute' => 'email',
                'options' => [
                    'class' => 'col-xs-3'
                ],
                'contentOptions' => [
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'format' => 'email',
            ],

            [
                'header' => "Визит",
                'attribute' => 'last_login',
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
                'content' => function ($model) {

                    return date('d.m.y', $model->last_login);
                },
                'filter' => false
            ],

            [
                'attribute' => 'active',
                'class' => 'kartik\grid\BooleanColumn',

                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'vAlign' => 'middle',
                'trueIcon' => "<i class = 'fa fa-user text-success'></i>",
                'falseIcon' => "<span class='fa-stack'>
                                  <i class='fa fa-user fa-stack-1x text-danger'></i>
                                  <i class='fa fa-ban fa-stack-2x text-danger'></i>
                                </span>",
                'hAlign' => 'center',
                'trueLabel' => 'Активен',
                'falseLabel' => 'Заблокирован',

            ],

            [
                'attribute' => 'first_name',
                'options' => [
                    'class' => 'col-xs-3'
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
                'attribute' => 'last_name',
                'options' => [
                    'class' => 'col-xs-2'
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
                'header' => 'Подписка',
                'class' => 'kartik\grid\BooleanColumn',
                'attribute' => 'notifications',
                'vAlign' => 'middle',
                'hAlign' => 'center',
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
            ],
        ],
    ]); ?>

    </div>
</div>
