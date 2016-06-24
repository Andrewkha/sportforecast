<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 6/24/2016
 * Time: 12:43 PM
 */

use app\components\grid\extendedGridView;
use yii\helpers\Html;
use yii\bootstrap\Collapse;
use app\models\teams\Teams;
use yii\bootstrap\ActiveForm;

?>
<div class="row">
    <div class = "col-xs-12">
        <h3 class = 'text-center'>Ближайшие матчи</h3>
        <?php foreach($games as $tournament):?>
            <?php $tournamentModel = \app\models\tournaments\Tournaments::findOne(['id_tournament' => $tournament['id_tournament']]);?>
            <?php $content = '';?>
            <?php if(time() < $tournamentModel->wfDueTo):?>
                <?php $content .= "Вы можете ". Html::a('сделать прогноз на призеров турнира ', ['tournaments/details', 'id' => $tournament['id_tournament']])."и заработать дополнительные очки"?>
            <?php endif;?>
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
