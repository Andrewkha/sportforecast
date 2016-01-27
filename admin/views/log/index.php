<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\widgets\Alert;

/* @var $this yii\web\View */
/* @var $searchModel app\models\countries\CountriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Журнал';
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 log-index">

        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <div class="row">
            <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'options' => [
                'class' => 'col-xs-12 col-md-10 col-lg-8'
            ],
            'rowOptions' => function($model, $key, $index, $grid) {

                return ['class' => $model->class];
            },
            'columns' => [

                [
                    'attribute' => 'id',
                    'options' => [
                        'class' => 'col-xs-1'
                    ],
                    'contentOptions' => [
                        'align' => 'center',
                    ],
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                ],

                [
                    'attribute' => 'log_time',
                    'value' => function($model) {
                        return date('d.m.Y H:i', $model->log_time);
                    },
                    'options' => [
                        'class' => 'col-xs-2'
                    ],
                    'contentOptions' => [
                        'align' => 'center',
                    ],
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'filter' => false,
                ],

                [
                    'attribute' => 'level',
                    'content' => function($model) {
                        return $model->status;
                    },
                    'options' => [
                        'class' => 'col-xs-2',
                    ],
                    'filter' => $typeFilter,
                ],

                [
                    'attribute' => 'message',
                    'options' => [
                        'class' => 'col-xs-7',
                    ],
                    'filter' => false
                ],
            ],
        ]); ?>
        </div>
    </div>
</div>