<?php

use kartik\widgets\Growl;
use yii\helpers\Html;
use app\components\widgets\News;
use app\components\grid\extendedGridView;
use app\models\users\UsersTournaments;
use app\components\widgets\frontPageGames;

/* @var $this yii\web\View */
$this->title = 'Сайт спортивных прогнозов';
?>

<div class = "row">
    <div class="col-xs-10 col-xs-offset-1 text-center">
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
        <?php
        if(Yii::$app->session->hasFlash('error')) {

            echo Growl::widget([
                'type' => Growl::TYPE_DANGER,
                'icon' => 'glyphicon glyphicon-flag',
                'body' => Yii::$app->session->getFlash('error'),
                'showSeparator' => true,
                'pluginOptions' => [
                    'placement' => [
                        'align' => 'left'
                    ]
                ]
            ]);
        }
        ?>

        <h2>Добро пожаловать на сайт спортивных прогнозов!</h2>
    </div>
</div>

<div class="body-content">

    <div class = "row">
        <div class = "col-xs-12 col-xs-offset-0 col-md-7 col-md-offset-1">
            <?= frontPageGames::widget(['type' => 'future']);?>
            <?= frontPageGames::widget(['type' => 'recent']);?>
        </div>

        <div class = "col-xs-8 col-xs-offset-0 col-md-3" id = 'right'>

            <div class = "text-center">
                <div class ="row">
                    <?= extendedGridView::widget([
                        'dataProvider' => $userTournaments,
                        'emptyText' => 'Нет текущих турниров',
                        'condensed' => true,
                        'caption' => 'Ваши текущие турниры',
                        'captionOptions' => [
                            'bordered' => false,
                            'class' => 'text-center',
                            'style' => 'font-size: 1.5em;'
                        ],
                        'bordered' => false,
                        'summary' => '',
                        'columns' => [

                            [
                                'header' => 'Турнир',
                                'contentOptions' => [
                                    'class' => 'text-left',
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-7'
                                ],
                                'content' => function($model) {
                                    return Html::a($model->tournament_name, ['tournaments/details', 'id' => $model->id_tournament]);
                                },
                                'format' => 'url',
                            ],

                            [
                                'header' => 'Ваши очки',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-4'
                                ],
                                'content' => function($model) {
                                    $points = UsersTournaments::find()->findModel($model->id_tournament, Yii::$app->user->id)->one()->totalPoints;
                                    return ($points === NULL)? '-' : $points;
                                }
                            ],

                            [
                                'header' => 'Место',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-1'
                                ],
                                'content' => function($model) {
                                    return UsersTournaments::find()->findModel($model->id_tournament, Yii::$app->user->id)->one()->position;
                                },
                            ],
                        ]
                    ])?>
                </div>
            </div>

            <div class = "text-center">
                <div class ="row">
                    <?= extendedGridView::widget([
                        'dataProvider' => $tournaments,
                        'showOnEmpty' => false,
                        'emptyText' => '',
                        'caption' => 'Остальные текущие турниры',
                        'captionOptions' => [
                            'bordered' => false,
                            'class' => 'text-center',
                            'style' => 'font-size: 1.5em;'
                        ],
                        'condensed' => true,
                        'bordered' => false,
                        'summary' => '',
                        'columns' => [

                            [
                                'header' => 'Турнир',
                                'contentOptions' => [
                                    'class' => 'text-left',
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-6'
                                ],
                                'content' => function($model) {
                                    return Html::a($model->tournament_name, ['tournaments/details', 'id' => $model->id_tournament]);
                                },
                                'format' => 'url',
                            ],

                            [
                                'header' => 'Лидер прогноза',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-4'
                                ],
                                'content' => function($model) {
                                    return (isset($model->usersTournaments[0]->idUser->username))? $model->usersTournaments[0]->idUser->username :'-';
                                },
                            ],

                            [
                                'header' => 'Очки лидера',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-1'
                                ],
                                'content' => function($model) {
                                    return (isset($model->usersTournaments[0]->points))? $model->usersTournaments[0]->points :'-';
                                },
                            ],

                            [
                                'header' => 'Участвовать',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-1'
                                ],
                                'content' => function($model) {
                                    return Html::a('Участвовать!', ['tournaments/participate', 'id' => $model->id_tournament], [
                                        'class' => 'btn btn-success btn-xs',
                                        'data-method' => 'post'
                                    ]);
                                }
                            ]
                        ]
                    ])

                    ?>
                    <p class = 'text-right'>
                        <?= Html::a("<i class = 'fa fa-futbol-o'></i> Все турниры", ['tournaments/index']);?>
                    </p>
                </div>

            </div>

            <div class = "text-center">
                <div class ="row">
                    <?= extendedGridView::widget([
                        'dataProvider' => $finishedTournaments,
                        'showOnEmpty' => false,
                        'emptyText' => '',
                        'caption' => 'Законченные турниры',
                        'captionOptions' => [
                            'bordered' => false,
                            'class' => 'text-center',
                            'style' => 'font-size: 1.5em;'
                        ],
                        'condensed' => true,
                        'bordered' => false,
                        'summary' => '',
                        'columns' => [

                            [
                                'header' => 'Турнир',
                                'contentOptions' => [
                                    'class' => 'text-left',
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-6'
                                ],
                                'content' => function($model) {
                                    return Html::a($model->tournament_name, ['tournaments/details', 'id' => $model->id_tournament]);
                                },
                                'format' => 'url',
                            ],

                            [
                                'header' => 'Победитель',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-4'
                                ],
                                'content' => function($model) {
                                    return (isset($model->usersTournaments[0]->idUser->username))? $model->usersTournaments[0]->idUser->username :'-';
                                },
                            ],

                            [
                                'header' => 'Очки победителя',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-1'
                                ],
                                'content' => function($model) {
                                    return (isset($model->usersTournaments[0]->points))? $model->usersTournaments[0]->points :'-';
                                },
                            ],
                        ]
                    ])

                    ?>
                    <p class = 'text-right'>
                        <?= Html::a("<i class = 'fa fa-futbol-o'></i> Все турниры", ['tournaments/index']);?>
                    </p>
                </div>

            </div>


            <div class="text-center">
                <div class = "row">
                    <?= News::widget(['title' => 'Новости']);?>
                </div>
            </div>
        </div>
    </div>
</div>