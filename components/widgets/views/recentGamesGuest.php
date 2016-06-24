<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 6/24/2016
 * Time: 12:53 PM
 */

use yii\helpers\Html;
use app\components\grid\extendedGridView;
use yii\bootstrap\Collapse;
?>

<div class = "row">
    <div class = "col-xs-12">
        <h3 class = 'text-center'>Недавно завершившиеся матчи</h3>
        <?php foreach($games as $tournament):?>

            <?php $content = '';?>
            <?php $content .= "<p class = 'text-right'>".Html::a("<i class = 'fa fa-list-alt'></i> Все игры", ['tournaments/details', 'id' => $tournament['id_tournament']])."</p>";?>
            <?php foreach($tournament['games'] as $k=> $tour):?>

                <?php $content .= extendedGridView::widget([
                    'dataProvider' => $tour,
                    'summary' => '',
                    'showHeader' => false,
                    'bordered' => false,
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

