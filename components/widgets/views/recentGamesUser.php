<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 6/24/2016
 * Time: 12:38 PM
 */
use app\components\grid\extendedGridView;
use yii\helpers\Html;
use yii\bootstrap\Collapse;
use app\models\teams\Teams;
?>

<div class = "row">
    <div class = "col-xs-12">
        <h3 class = 'text-center'>Недавно завершившиеся матчи</h3>
        <?php foreach($games as $tournament):?>

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
