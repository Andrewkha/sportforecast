<?php

use yii\helpers\Html;
use app\models\tournaments\Tournaments;
use kartik\widgets\ActiveForm;
use kartik\widgets\Growl;
use app\components\grid\extendedGridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\tournaments\TournamentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Турниры');

$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10">
        <?php
        if(Yii::$app->session->hasFlash('success')) {

            echo Growl::widget([
                'type' => Growl::TYPE_SUCCESS,
                'icon' => 'glyphicon glyphicon-ok-sign',
                'body' => Yii::$app->session->getFlash('success'),
                'showSeparator' => true,
                'pluginOptions' => [
                    'placement' => [
                        'align' => 'left'
                    ]
                ]
            ]);
        }
        ?>
        <h2>Ваши турниры</h2>
        <h4>Кликните на название турнира, чтобы сделать прогнозы и посмотреть результаты</h4>

        <div class="row">
            <div class="panel panel-default 'col-xs-12 col-lg-9">
                <div class = "panel-body text-center text-danger">
                    <strong>Внимание! Если вы отказываетесь от участия в турнире, все ваши прогнозы будут удалены!!!</strong>
                </div>
            </div>
        </div>

        <?php $form = ActiveForm::begin([
            'action' => ['tournaments/notification'],
            'type' => ActiveForm::TYPE_INLINE,
            'fieldConfig' => [
                'autoPlaceholder' => false,
            ]
        ]);?>

            <div class="row">
                <?= extendedGridView::widget([
                    'dataProvider' => $userTournaments,
                    'bordered' => false,
                    'summary' => false,
                    'options' => [
                        'class' => 'col-xs-12 col-lg-9'
                    ],
                    'resizableColumns' => false,
                    'columns' => [

                        [
                            'header' => 'Название турнира',
                            'contentOptions' => [
                                'style' => 'vertical-align:middle',
                            ],
                            'headerOptions' => [
                                'style' => 'text-align:center',
                            ],
                            'options' => [
                                'class' => 'col-xs-2 col-lg-3'
                            ],
                            'content' => function($model) {
                                return Html::a($model->idTournament->tournament_name, ['tournaments/details', 'id' => $model->id_tournament]);
                            },

                            'format' => 'url',
                        ],

                        [
                            'header' => 'Страна',
                            'attribute' => 'idTournament.country0.country',
                            'contentOptions' => [
                                'align' => 'center',
                                'style' => 'vertical-align:middle',
                            ],
                            'headerOptions' => [
                                'style' => 'text-align:center',
                            ],
                            'options' => [
                                'class' => 'col-xs-2 col-lg-1'
                            ],
                        ],

                        [
                            'header' => 'Статус',
                            'attribute' => 'idTournament.status',
                            'options' => [
                                'class' => 'col-xs-2 col-lg-1'
                            ],
                            'headerOptions' => [
                                'style' => 'text-align:center',
                            ],
                            'contentOptions' => [
                                'align' => 'center',
                                'style' => 'vertical-align:middle',
                            ],
                        ],

                        [
                            'header' => 'Место',
                            'filter' => false,
                            'contentOptions' => [
                                'style' => 'vertical-align:middle',
                                'align' => 'center'
                            ],
                            'headerOptions' => [
                                'style' => 'text-align:center',
                            ],
                            'options' => [
                                'class' => 'col-xs-1'
                            ],

                            'content' => function($model){
                                return \app\models\users\UsersTournaments::find()->findModel($model->id_tournament, Yii::$app->user->id)->one()->position;
                            }
                        ],

                        [
                            'header' => 'Очки',
                            'filter' => false,
                            'contentOptions' => [
                                'style' => 'vertical-align:middle',
                                'align' => 'center'
                            ],
                            'headerOptions' => [
                                'style' => 'text-align:center',
                            ],
                            'options' => [
                                'class' => 'col-xs-1'
                            ],
                            'content' => function($model){
                                return \app\models\users\UsersTournaments::find()->findModel($model->id_tournament, Yii::$app->user->id)->one()->totalPoints;
                            }
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
                                'class' => 'col-xs-1 col-lg-2'
                            ],
                            'attribute' => 'idUser.username',

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
                                'class' => 'col-xs-1'
                            ],
                            'attribute' => 'points',
                        ],

                        [
                            'attribute' => 'notification',
                            'hAlign' => 'center',
                            'vAlign' => 'middle',
                            'options' => [
                                'class' => 'col-xs-1'
                            ],
                            'headerOptions' => [
                                'style' => 'text-align:center',
                            ],
                            'content' => function($model) use ($form){

                                return Html::activeHiddenInput($model, "[$model->id_tournament]id_tournament").$form->field($model, "[$model->id_tournament]notification")->checkbox(['label' => '']);

                            },
                            'header' => 'Новости'
                        ],

                        [
                            'hAlign' => 'center',
                            'vAlign' => 'middle',

                            'options' => [
                                'class' => 'col-xs-1',
                            ],

                            'content' => function($model) {

                                return Html::a("<i class='fa fa-chain-broken'></i>", ['tournaments/delete', 'id' => $model['id_tournament']], [
                                   // 'data-method' => 'post',
                                    //'data-pjax' => 0,
                                    'data-confirm' => 'Уверены? Все ваши прогнозы на этот турнир будут удалены!',
                                    'title' => 'Прекратить участие в турнире'
                                ]);
                            },
                            'header' => ''
                        ],
                    ],
                ]); ?>

                <div class="col-xs-12 col-lg-9">
                    <p class="text-right">
                        <?= Html::submitButton('Применить', ['class' => 'btn btn-success text-right']);?>
                    </p>
                </div>
            </div>
        <?php ActiveForm::end();?>
        <hr>
        <h2>Ваши законченные турниры</h2>
        <h4>Кликните на название турнира, чтобы посмотреть результаты</h4>
        <div class="row">
            <?= extendedGridView::widget([
                'dataProvider' => $userFinishedTournaments,
                'bordered' => false,
                'summary' => false,
                'options' => [
                    'class' => 'col-xs-12 col-lg-9'
                ],
                'resizableColumns' => false,
                'columns' => [

                    [
                        'header' => 'Название турнира',
                        'contentOptions' => [
                            'style' => 'vertical-align:middle',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-xs-3 col-lg-5'
                        ],
                        'content' => function($model) {
                            return Html::a($model->idTournament->tournament_name, ['tournaments/details', 'id' => $model->id_tournament]);
                        },

                        'format' => 'url',
                    ],

                    [
                        'header' => 'Страна',
                        'contentOptions' => [
                            'align' => 'center',
                            'style' => 'vertical-align:middle',
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'attribute' => 'idTournament.country0.country',
                        'options' => [
                            'class' => 'col-xs-2 col-lg-1'
                        ],
                    ],

                    [
                        'header' => 'Место',
                        'contentOptions' => [
                            'style' => 'vertical-align:middle',
                            'align' => 'center'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                        'content' => function($model){
                            return \app\models\users\UsersTournaments::find()->findModel($model->id_tournament, Yii::$app->user->id)->one()->position;
                        }

                    ],

                    [
                        'header' => 'Очки',
                        'contentOptions' => [
                            'style' => 'vertical-align:middle',
                            'align' => 'center'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                        'content' => function($model){
                            return \app\models\users\UsersTournaments::find()->findModel($model->id_tournament, Yii::$app->user->id)->one()->totalPoints;
                        }
                    ],

                    [
                        'header' => 'Победитель',
                        'contentOptions' => [
                            'style' => 'vertical-align:middle',
                            'align' => 'center'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-xs-2'
                        ],
                        'attribute' => 'idUser.username',

                    ],

                    [
                        'header' => 'Очки лидера',
                        'contentOptions' => [
                            'style' => 'vertical-align:middle',
                            'align' => 'center'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                        'attribute' => 'points',
                    ],
                ],
            ]); ?>
        </div>

        <hr>
        <h2>Турниры без вашего участия</h2>
        <h4>Кликните на название турнира, чтобы посмотреть результаты</h4>
        <div class="row">
            <?= extendedGridView::widget([
                'dataProvider' => $notUserTournaments,
                'bordered' => false,
                'summary' => false,
                'options' => [
                    'class' => 'col-xs-12 col-sm-10 col-lg-8'
                ],
                'resizableColumns' => false,
                'columns' => [

                    [
                        'header' => 'Название турнира',
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
                            return Html::a($model->idTournament->tournament_name, ['tournaments/details', 'id' => $model->id_tournament]);
                        },

                        'format' => 'url',
                    ],

                    [
                        'header' => 'Страна',
                        'attribute' => 'idTournament.country0.country',
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
                    ],

                    [
                        'header' => 'Статус',
                        'content' => function($model) {
                            return $model->idTournament->status;
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
                            'class' => 'col-xs-2'
                        ],
                        'attribute' => 'idUser.username',

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
                            'class' => 'col-xs-1'
                        ],
                        'attribute' => 'points',
                    ],

                    [
                        'header' => 'Участвовать!',
                        'contentOptions' => [
                            'style' => 'vertical-align:middle',
                            'align' => 'center'
                        ],
                        'headerOptions' => [
                            'style' => 'text-align:center',
                        ],
                        'options' => [
                            'class' => 'col-xs-1'
                        ],
                        'content' => function($model) {

                            if ($model['idTournament']['is_active'] == Tournaments::GOING ||
                                $model['idTournament']['is_active'] == Tournaments::NOT_STARTED
                            )

                                return Html::a('Участвовать!', ['tournaments/participate', 'id' => $model->id_tournament], [
                                    'class' => 'btn btn-success btn-sm',
                                    'data-method' => 'post'
                                ]);

                            else return '-';
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
