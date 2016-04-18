<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\ActiveForm;
use app\models\games\Games;

?>

<?php $form = ActiveForm::begin([
    'action' => '',
])?>

        <div class="form-group col-xs-12">
            <?= Html::dropDownList('tours', null, $tour_list, ['class' => 'form-control', 'multiple' => 'multiple', 'size' => 5, 'style' => 'width:100px']);?>
        </div>
        <div class="form-group col-xs-12">
            <?= Html::submitButton('Выбрать', ['class' => 'btn btn-primary']) ?>
        </div>

<?php ActiveForm::end(); ?>

<?php $count = 0;?>

<?php $unfinishedTours = Games::listOfUnfinishedTours($tournament->id_tournament);?>
<?php foreach($tourGames as $tour=> $games):?>
<div class="col-xs-12 col-sm-10 col-lg-6">
    <?php if(!in_array($tour, $unfinishedTours)) :?>
        <?= GridView::widget([
            'dataProvider' => $tourGames[$tour],
            'responsive' => false,
            'hover' => true,
            'showPageSummary' => true,
            'responsiveWrap' => false,
            'export' => false,
            'bordered' => false,
            'showHeader' => false,
            'caption' => "Тур $tour",
            'summary' => '',
            'columns' => [

                [
                    'attribute' => 'date_time_game',
                    'content' => function($model) {
                        return '<strong>'.date('d.m.y H:i', $model['date_time_game']).'</strong>';
                    },
                    'contentOptions' => [
                        'class' => 'reduceDateFont'
                    ],
                    'options' => [
                        'class' => 'col-xs-1'
                    ],
                    'hAlign' => 'center',
                    'vAlign' => 'middle',
                ],

                [
                    'content' => function($model){
                        return

                            "<row>".
                                "<div class = 'text-right col-xs-4'>".
                                    $model['idTeamHome']['idTeam']['team_name'].
                                    " ".
                                "</div>".
                                "<div class = 'text-center col-xs-4'>".
                                    "<strong>".$model['score_home']." - ".$model['score_guest']."</strong>".
                                "</div>".
                                "<div class = 'text-left col-xs-4'>".
                                    " ".$model['idTeamGuest']['idTeam']['team_name'].
                                "</div>".
                            "</row>".

                            "<div class='clearfix visible-xs-block visible-sm-block visible-md-block visible-lg-block'></div>".
                            "<row>".
                                "<div class = 'col-xs-4 col-xs-offset-4 col-sm-2 col-sm-offset-5 text-center'>".
                                    Html::tag('div', (isset($model['f_id']))? $model['fscore_home'].' - '.$model['fscore_guest']
                                    : " - ", [
                                        'class' => (!isset($model['f_id']))? '' :
                                            (($model['fpoints'] == 0)? 'bg-danger':
                                                (($model['fpoints'] == 1)? 'bg-info' :
                                                    (($model['fpoints'] == 3)? 'bg-success':
                                                        (($model['fpoints'] == 2)? 'bg-warning':''))))
                                    ]).
                                "</div>".
                                "<div class = 'col-xs-4 col-sm-2 col-sm-offset-3 text-right'>".
                                    Html::tag('span', (isset($model['f_id']))? "Очки: ".$model['fpoints'] :'' ,[
                                    ]).
                                "</div>".
                            "</row>"
                            ;
                    },
                    'options' => [
                        'class' => 'col-xs-11'
                    ],
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
                    'hAlign' => 'right',
                    'vAlign' => 'middle',
                ],
            ]
        ]);?>

    <?php else :?>
        <?php $form = ActiveForm::begin([
            'action' => ['site/forecast-save']
        ]);?>
        <?= GridView::widget([
            'dataProvider' => $tourGames[$tour],
            'responsive' => false,
            'hover' => true,
            'showPageSummary' => true,
            'responsiveWrap' => false,
            'export' => false,
            'bordered' => false,
            'showHeader' => false,
            'caption' => "Тур $tour",
            'summary' => '',
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
                    'content' => function($model) use ($form){
                        return
                            (isset($model['score_home']))?
                                "<row>".
                                    "<div class = 'text-right col-xs-5'>".
                                        $model['idTeamHome']['idTeam']['team_name'].
                                        " ".
                                    "</div>".
                                    "<div class = 'text-center col-xs-2'>".
                                        "<strong>".$model['score_home']." - ".$model['score_guest']."</strong>".
                                    "</div>".
                                    "<div class = 'text-left col-xs-5'>".
                                        " ".$model['idTeamGuest']['idTeam']['team_name'].
                                "</div>".
                                "</row>".

                                "<div class='clearfix visible-xs-block'></div>".
                                "<row>".
                                    "<div class = 'col-xs-2 col-xs-offset-5 text-center'>".
                                        Html::tag('div', (isset($model['f_id']))? $model['fscore_home'].' - '.$model['fscore_guest']
                                            : " - ", [
                                            'class' => (!isset($model['f_id']))? '' :
                                                (($model['fpoints'] == 0)? 'bg-danger':
                                                    (($model['fpoints'] == 1)? 'bg-info' :
                                                        (($model['fpoints'] == 3)? 'bg-success':
                                                            (($model['fpoints'] == 2)? 'bg-warning':''))))
                                        ]).
                                    "</div>".
                                    "<div class = 'col-xs-2 col-xs-offset-3 text-right'>".
                                        Html::tag('span', (isset($model['f_id']))? "Очки: ".$model['fpoints'] :'' ,[]).
                                    "</div>".
                                "</row>"
                                :
                                "<row>".
                                    "<div class = 'text-right col-xs-5'>".
                                        $model['idTeamHome']['idTeam']['team_name'].
                                        " ".
                                    "</div>".
                                    "<div class = 'text-center col-xs-2'>".
                                        ' - '.
                                    "</div>".
                                    "<div class = 'text-left col-xs-5'>".
                                        " ".$model['idTeamGuest']['idTeam']['team_name'].
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
                    'attribute' => 'fpoints',
                    'pageSummary' => function($summary, $data, $widget){

                        return
                            "<div class = 'row'>"
                                ."<div class = 'col-xs-11'>"
                                    ."<p class = 'pull-left'>Всего очков в туре:</p>"
                                ."</div>"
                                ."<div class = 'col-xs-1'>"
                                    ."<p class = 'pull-left'>$summary</p>"
                                ."</div>"
                            ."</div>";
                    },
                    'vAlign' => 'middle',
                ],

            ]
        ]);?>
        <p class = 'pull-right'><?=Html::submitButton('Сохранить',['class' => 'btn btn-success', 'form' => $form->getId()]);?></p>
        <?php ActiveForm::end();?>

    <?php endif;?>
</div>
    <?php $count++;
    if($count%2 == 0) :?>
        <div class="clearfix visible-lg-block"></div>
    <?php endif;?>
<?php endforeach;?>

