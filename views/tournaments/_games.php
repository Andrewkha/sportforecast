<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\ActiveForm;

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

<?php foreach($tourGames as $tour=> $games):?>
    <?= GridView::widget([
        'dataProvider' => $tourGames[$tour],
        'responsive' => false,
        'responsiveWrap' => false,
        'hover' => true,
        'export' => false,
        'bordered' => false,
        'showHeader' => false,
        'caption' => "Тур $tour",
        'options' => [
            'class' => 'col-xs-12 col-sm-10 col-md-8 col-lg-6'
        ],
        'summary' => '',
        'columns' => [

            [
                'attribute' => 'date_time_game',
                'content' => function($model) {
                    return '<strong>'.date('d.m.y H:i', $model->date_time_game).'</strong>';
                },
                'options' => [
                    'class' => 'col-xs-1 col-sm-2'
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
                    'class' => 'col-xs-3 col-sm-2'
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
    ]);?>
    <?php
        $count++;
        if($count%2 == 0) :
    ?>
            <div class="clearfix visible-lg-block"></div>
    <?php
        endif;
    ?>
<?php endforeach;?>

