<?php

use kartik\widgets\Growl;
use yii\helpers\Html;
use yii\bootstrap\Collapse;
use yii\bootstrap\ActiveForm;
use app\components\widgets\News;

use app\components\grid\extendedGridView;
use app\models\teams\Teams;
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

            <div class="row">
                <div class = "col-xs-12">
                    <h3 class = 'text-center'>Ближашие матчи</h3>
                    <?php foreach($futureGames as $tournament):?>

                        <?php $content = '';?>
                        <?php $content .= "<p class = 'text-right'>".Html::a("<i class = 'fa fa-list-alt'></i> Все игры", ['tournaments/details', 'id' => $tournament['id_tournament']])."</p>";?>
                            <?php foreach($tournament['games'] as $k=> $tour):?>
                            <?php $form = ActiveForm::begin([
                                'action' => ['site/forecast-save']
                            ]);?>
                                <?php $content .= extendedGridView::widget([
                                    'dataProvider' => $tour,
                                    'summary' => '',
                                    'bordered' => false,
                                    'showHeader' => false,
                                    'caption' => "Тур $k. Ваш прогноз",
                                    'captionOptions' => [
                                        'colspan' => 4, 'class' => 'text-center normal'
                                    ],
                                    'columns' => [

                                        [
                                            'content' => function($model) {
                                                return '<strong>'.date('d.m.y H:i', $model['date_time_game']).'</strong>';
                                            },
                                            'options' => [
                                                'class' => 'col-xs-1',
                                            ],
                                            'contentOptions' => [
                                                'class' => 'reduceDateFont'
                                            ],
                                            'hAlign' => 'center',
                                            'vAlign' => 'middle',
                                        ],

                                        [
                                            'class' => 'kartik\grid\ExpandRowColumn',
                                            'value' => function ($model, $key, $index, $column) {
                                                return extendedGridView::ROW_COLLAPSED;
                                            },
                                            'detail' => function ($model, $key, $index, $column) {
                                                return Yii::$app->controller->renderPartial('_five-last-games', ['details'=>$model]);
                                            },
                                            'headerOptions' => ['class'=>'kartik-sheet-style'],
                                            'expandOneOnly' => true,
                                            'expandIcon' => "<i class='fa fa-plus-square'></i>",
                                            'collapseIcon' => "<i class='fa fa-minus-square'></i>",
                                            'enableRowClick' => true,
                                            'detailOptions' => [
                                                'class' => 'expanded'
                                            ],
                                            'contentOptions' => [
                                                'class' => 'expand-icon',
                                            ]
                                        ],
                                        [
                                            'content' => function($model) use ($form){
                                                return
                                                "<row>".
                                                    "<div class = 'text-right col-xs-5'>".
                                                        Html::img(Teams::getPath().'/'.$model['idTeamHome']['idTeam']['team_logo'], ['width' => 30]).' '.Html::a($model['idTeamHome']['idTeam']['team_name'],['tournaments/games', 'id' => $model['id_team_home']]).
                                                    " ".
                                                    "</div>".
                                                    "<div class = 'text-center col-xs-2'>".
                                                        "-".
                                                    "</div>".
                                                    "<div class = 'text-left col-xs-5'>".
                                                        " ".Html::a($model['idTeamGuest']['idTeam']['team_name'], ['tournaments/games', 'id' => $model['id_team_guest']]).' '.Html::img(Teams::getPath().'/'.$model['idTeamGuest']['idTeam']['team_logo'], ['width' => 30]).
                                                    "</div>".
                                                "</row>".
                                                "<row>".
                                                    "<div class = 'col-xs-12 text-center'>".
                                                        Html::input('number', "forecasts[$model[id_game]][fscore_home]",(isset($model['f_id']))? $model['fscore_home'] : '',[
                                                            'class' => 'forecast',
                                                            'form' => $form->getID(),
                                                            'maxlength'=> 2,
                                                            'disabled' => ($model['date_time_game'] - time() < 60*60 )? true : false,
                                                        ]).
                                                        Html::input('number', "forecasts[$model[id_game]][fscore_guest]",(isset($model['f_id']))? $model['fscore_guest'] : '',[
                                                            'class' => 'forecast',
                                                            'maxlength' => 2,
                                                            'form' => $form->getID(),
                                                            'disabled' => ($model['date_time_game'] - time() < 60*60 )? true : false,
                                                        ]).
                                                    "</div>".
                                                "</row>"
                                                    ;
                                            },
                                            'options' => [
                                                'class' => 'col-xs-11'
                                            ],
                                            'contentOptions' => [
                                                'style' => 'font-size: 13px'
                                            ],
                                            'vAlign' => 'middle',
                                        ],

                                    ]
                                ]);
                                ?>
                                <?php $content .= "<p class = 'text-right'>".Html::submitButton('Сохранить',['class' => 'btn btn-success', 'form' => $form->getId()])."</p>";?>
                            <?php ActiveForm::end();?>
                            <?php $content .="</form>"?>
                        <?php endforeach;?>

                        <?= Collapse::widget([
                            'encodeLabels' => false,
                            'items' => [
                                [
                                    'label' => "<i class = 'fa fa-futbol-o fa-spin'></i>".'  '.$tournament['tournament'],
                                    'content' => $content,
                                ]
                            ]
                        ])
                        ?>
                    <?php endforeach;?>
                </div>
            </div>
            <div class = "row">
                <div class = "col-xs-12">
                    <h3 class = 'text-center'>Недавно завершившиеся матчи</h3>
                     <?php foreach($recentGames as $tournament):?>

                        <?php $content = '';?>
                        <?php $content .= "<p class = 'text-right'>".Html::a("<i class = 'fa fa-list-alt'></i> Все игры", ['tournaments/details', 'id' => $tournament['id_tournament']])."</p>";?>
                        <?php foreach($tournament['games'] as $k => $tour):?>
                            <?php $content .= extendedGridView::widget([
                                'dataProvider' => $tour,
                                'summary' => '',
                                'showHeader' => false,
                                'showPageSummary' => true,
                                'caption' => "Тур $k",
                                'captionOptions' => [
                                    'colspan' => 4, 'class' => 'text-center normal'
                                ],

                                'columns' => [

                                    [
                                        'content' => function($model) {
                                            return '<strong>'.date('d.m.y H:i', $model['date_time_game']).'</strong>';
                                        },
                                        'options' => [
                                            'class' => 'col-xs-1'
                                        ],
                                        'contentOptions' => [
                                            'class' => 'reduceDateFont'
                                        ],
                                        'hAlign' => 'center',
                                        'vAlign' => 'middle',
                                    ],

                                    [
                                        'content' => function($model){
                                            return

                                                "<row>".
                                                    "<div class = 'text-right col-xs-4'>".
                                                        Html::img(Teams::getPath().'/'.$model['idTeamHome']['idTeam']['team_logo'], ['width' => 30]).' '.$model['idTeamHome']['idTeam']['team_name'].
                                                        " ".
                                                    "</div>".
                                                    "<div class = 'text-center col-xs-4'>".
                                                       "<strong>".$model['score_home']." - ".$model['score_guest']."</strong>".
                                                    "</div>".
                                                    "<div class = 'text-left col-xs-4'>".
                                                        " ".$model['idTeamGuest']['idTeam']['team_name'].' '.Html::img(Teams::getPath().'/'.$model['idTeamGuest']['idTeam']['team_logo'], ['width' => 30]).
                                                    "</div>".
                                                "</row>".
                                                "<div class='clearfix visible-xs-block'></div>".
                                                "<row>".
                                                    "<div class = 'col-xs-4 col-xs-offset-4 col-lg-2 col-lg-offset-5 text-center'>".
                                                        Html::tag('div', (isset($model['f_id']))? $model['fscore_home'].' - '.$model['fscore_guest']
                                                            : " - ", [
                                                            'class' => (!isset($model['f_id']))? '' :
                                                                (($model['fpoints'] == 0)? 'bg-danger':
                                                                    (($model['fpoints'] == 1)? 'bg-info' :
                                                                        (($model['fpoints'] == 3)? 'bg-success':
                                                                            (($model['fpoints'] == 2)? 'bg-warning':''))))
                                                        ]).
                                                    "</div>".
                                                    "<div class = 'col-xs-3 col-xs-offset-1 col-lg-2 col-lg-offset-3 text-right'>".
                                                        Html::tag('span', (isset($model['f_id']))? "Очки: ".$model['fpoints'] :'' ,[
                                                        ]).
                                                    "</div>".
                                                "</row>"
                                                ;
                                        },

                                        'attribute' => 'fpoints',
                                        'pageSummary' => function($summary, $data, $widget){

                                            return
                                                "<div class = 'row'>"
                                                    ."<div class = 'col-xs-10'>"
                                                        ."<p class = 'pull-left'>Всего очков в туре:</p>"
                                                    ."</div>"
                                                    ."<div class = 'col-xs-2'>"
                                                        ."<p class = 'pull-left'>$summary</p>"
                                                    ."</div>"
                                                ."</div>";
                                        },
                                        'options' => [
                                            'class' => 'col-xs-11'
                                        ],
                                        'contentOptions' => [
                                            'style' => 'font-size: 13px'
                                        ],
                                        'vAlign' => 'middle',
                                    ],
                                ]
                            ]);
                            ?>
                        <?php endforeach;?>

                        <?= Collapse::widget([
                            'encodeLabels' => false,
                            'items' => [
                                [
                                    'label' => "<i class = 'fa fa-futbol-o fa-spin'></i>".'  '.$tournament['tournament'],
                                    'content' => $content,
                                ]
                            ]
                        ])
                        ?>
                    <?php endforeach;?>
                </div>
            </div>

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
                                    return Html::a($model->idTournament->tournament_name, ['tournaments/details', 'id' => $model->idTournament->id_tournament]);
                                },
                                'format' => 'url',
                            ],

                            [
                                'attribute' => 'userPoints',
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
                            ],

                            [
                                'attribute' => 'userPosition',
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
                                    return Html::a($model['idTournament']['tournament_name'], ['tournaments/details', 'id' => $model['id_tournament']]);
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
                                    return $model['idUser']['username'];
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
                                    return $model['points'];
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
                                    return Html::a('Участвовать!', ['tournaments/participate', 'id' => $model['id_tournament']], [
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

            <div class="text-center">
                <div class = "row">
                    <?= News::widget(['title' => 'Новости']);?>
                </div>
            </div>
        </div>
    </div>
</div>