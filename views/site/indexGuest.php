<?php
use kartik\widgets\Growl;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\bootstrap\Collapse;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
$this->title = 'Сайт спортивных прогнозов';
?>

<?php
if(Yii::$app->session->hasFlash('success')) {

    echo Growl::widget([
        'type' => Growl::TYPE_SUCCESS,
        'icon' => 'glyphicon glyphicon-ok-sign',
        'body' => Yii::$app->session->getFlash('success'),
        'showSeparator' => true,
    ]);
}
?>

<div class = "row">
    <div class="col-xs-10 col-xs-offset-1 text-center">
        <h2>Добро пожаловать на сайт спортивных прогнозов!</h2>
        <p class="lead"><?= Html::a('Войдите', ['site/login']);?>/<?= Html::a('зарегистрируйтесь', ['site/signup']);?> чтобы принять участие</p>
    </div>
</div>

<div class="body-content">

    <div class = "row">
        <div class = "col-xs-12 col-xs-offset-0 col-md-6 col-md-offset-1">
            <div class="row">
                <div class = "col-xs-12">
                    <h3 class = 'text-center'>Ближашие матчи</h3>
                    <?php foreach($futureGames as $tournament):?>

                        <?php $content = '';?>
                        <?php $content .= "<p class = 'text-right'>".Html::a("<i class = 'fa fa-list-alt'></i> Все игры", ['tournaments/details', 'id' => $tournament['id_tournament']])."</p>";?>
                        <?php foreach($tournament['games'] as $k=> $tour):?>

                            <?php $content .= GridView::widget([
                                    'dataProvider' => $tour,
                                    'export' => false,
                                    'summary' => '',
                                    'responsive' => false,
                                    'bordered' => false,
                                    'responsiveWrap' => false,
                                    'showHeader' => false,
                                    'caption' => "Тур $k",
                                    'captionOptions' => [
                                        'colspan' => 4, 'class' => 'text-center normal'
                                    ],
                                    'columns' => [

                                        [
                                            'content' => function($model) {
                                                return '<strong>'.date('d.m.y H:i', $model->date_time_game).'</strong>';
                                            },
                                            'options' => [
                                                'class' => 'col-xs-3'
                                            ],
                                            'contentOptions' => [
                                                'class' => 'reduceDateFont'
                                            ],
                                            'hAlign' => 'center',
                                            'vAlign' => 'middle',
                                        ],

                                        [
                                            'content' => function($model) {
                                                return Html::img($model->idTeamHome->idTeam->fileUrl, ['width' => 30]).' '.$model->idTeamHome->idTeam->team_name;
                                            },
                                            'options' => [
                                                'class' => 'col-xs-4'
                                            ],
                                            'hAlign' => 'right',
                                            'vAlign' => 'middle',
                                        ],

                                        [
                                            'content' => function($model) {
                                                return
                                                ' - '
                                                ;
                                            },
                                            'options' => [
                                                'class' => 'col-xs-1'
                                            ],
                                            'hAlign' => 'center',
                                            'vAlign' => 'middle',
                                        ],

                                        [
                                            'content' => function($model) {
                                                return $model->idTeamGuest->idTeam->team_name.' '.Html::img($model->idTeamGuest->idTeam->fileUrl, ['width' => 30]);
                                            },
                                            'options' => [
                                                'class' => 'col-xs-4'
                                            ],
                                            'hAlign' => 'left',
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

            <div class = "row">
                <div class = "col-xs-12">
                    <h3 class = 'text-center'>Недавно завершившиеся матчи</h3>
                    <?php foreach($recentGames as $tournament):?>

                        <?php $content = '';?>
                        <?php $content .= "<p class = 'text-right'>".Html::a("<i class = 'fa fa-list-alt'></i> Все игры", ['tournaments/details', 'id' => $tournament['id_tournament']])."</p>";?>
                        <?php foreach($tournament['games'] as $k=> $tour):?>

                            <?php $content .= GridView::widget([
                                'dataProvider' => $tour,
                                'export' => false,
                                'summary' => '',
                                'responsive' => false,
                                'showHeader' => false,
                                'bordered' => false,
                                'responsiveWrap' => false,
                                'caption' => "Тур $k",
                                'captionOptions' => [
                                    'colspan' => 4, 'class' => 'text-center normal'
                                ],

                                'columns' => [

                                    [
                                        'content' => function($model) {
                                            return '<strong>'.date('d.m.y H:i', $model->date_time_game).'</strong>';
                                        },
                                        'options' => [
                                            'class' => 'col-xs-2'
                                        ],
                                        'contentOptions' => [
                                            'class' => 'reduceDateFont'
                                        ],
                                        'hAlign' => 'center',
                                        'vAlign' => 'middle',
                                    ],

                                    [
                                        'content' => function($model) {
                                            return Html::img($model->idTeamHome->idTeam->fileUrl, ['width' => 30]).' '.$model->idTeamHome->idTeam->team_name;
                                        },
                                        'options' => [
                                            'class' => 'col-xs-4'
                                        ],
                                        'hAlign' => 'right',
                                        'vAlign' => 'middle',
                                    ],

                                    [
                                        'content' => function($model) {
                                            return
                                                "<strong>".$model->score_home.' - '.$model->score_guest."</strong>"
                                                ;
                                        },
                                        'options' => [
                                            'class' => 'col-xs-2'
                                        ],
                                        'hAlign' => 'center',
                                        'vAlign' => 'middle',
                                    ],

                                    [
                                        'content' => function($model) {
                                            return $model->idTeamGuest->idTeam->team_name.' '.Html::img($model->idTeamGuest->idTeam->fileUrl, ['width' => 30]);
                                        },
                                        'options' => [
                                            'class' => 'col-xs-4'
                                        ],
                                        'hAlign' => 'left',
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

        <div class = "col-xs-8 col-xs-offset-0 col-md-4 col-lg-3 col-lg-offset-1" id = 'right'>

            <div class = "text-center">
                <div class ="row">
                    <h3>Текущие турниры</h3>
                    <?= GridView::widget([
                        'dataProvider' => $tournaments,
                        'export' => false,
                        'emptyText' => 'Нет текущих турниров',
                        'responsiveWrap' => false,
                        'condensed' => true,
                        'bordered' => false,
                        'responsive' => false,
                        'summary' => '',
                        'columns' => [
                            [
                                'attribute' => 'tournament_name',
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
                                    return Html::a($model['tournament_name'], ['tournaments/details', 'id' => $model['id_tournament']]);
                                },
                                'format' => 'url',
                            ],

                            [
                                'attribute' => 'leader',
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
                            ],

                            [
                                'attribute' => 'leaderPoints',
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
                            ],
                        ]
                    ])

                    ?>
                </div>
                <p class = 'text-right'>
                    <?= Html::a("<i class = 'fa fa-futbol-o'></i> Все турниры", ['tournaments/index']);?>
                </p>
            </div>

            <div class="text-center">
                <div class = "row">
                    <h3>Новости</h3>
                    <?php foreach($news as $one):?>
                        <div class = "row news text-left">
                            <div class="col-xs-12">
                                <strong><?= ($one->id_tournament == 0) ? 'Новости сайта' : $one->tournament->tournament_name;?></strong>
                            </div>
                            <div class="col-xs-12">
                                <span class = "time"><?= date('d.m.Y H:i', $one->date);?></span>
                                <?php Modal::begin([
                                    'header' => "<h4>".$one->subject."</h4>",
                                    'headerOptions' => [
                                        'class' => 'bg-info'
                                    ],
                                    'size' => Modal::SIZE_LARGE,
                                    'toggleButton' => [
                                        'tag' => 'a',
                                        'label' => $one->subject,
                                        'style' => 'font-size: 1em; cursor: pointer;'
                                    ],
                                    'footer' =>
                                        "<div class='row'>".
                                            "<div class='col-xs-7'>".
                                                "<p class = 'pull-left'>Разместил: <strong>{$one->author0->username}</strong>"."</p>".
                                            "</div>".
                                            "<div class='col-xs-4'>".
                                                "<p class='pull-right'>".date('d.m.Y H:i', $one->date)."</p>".
                                            "</div>".
                                        "</div>",
                                ]);?>
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <?= $one->body;?>
                                    </div>
                                </div>
                                <?php Modal::end();?>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
                <p class = 'text-right'>
                    <a href = 'news'><i class = 'fa fa-newspaper-o'></i> Все новости</a>
                </p>
            </div>
        </div>
    </div>
</div>

