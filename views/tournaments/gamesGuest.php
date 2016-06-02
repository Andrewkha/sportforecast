<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\teams\Teams;

/**
    * @var $team
    * @var $tournament
    * @var $games
 */
?>
<?php
$this->title = 'Игры '.$team->idTeam->team_name.' в турнире '.$tournament->idTournament->tournament_name;
$this->params['breadcrumbs'][] = ['label' => 'Турниры', 'url' => 'tournaments'];
$this->params['breadcrumbs'][] = ['label' => $tournament->idTournament->tournament_name, 'url' => ['tournaments/details', 'id' => $tournament->idTournament->id_tournament]];
$this->params['breadcrumbs'][] = 'Игры '.$team->idTeam->team_name;

?>

<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10">
        <div class="row">
            <?= GridView::widget([
        'dataProvider' => $games,
        'responsive' => false,
        'responsiveWrap' => false,
        'hover' => true,
        'export' => false,
        'showHeader' => false,
        'bordered' => false,
        'caption' => $this->title,
        'options' => [
            'style' => 'margin-top: 30px',
            'class' => 'col-xs-12 col-sm-10 col-md-8 col-lg-6'
        ],
        'summary' => '',
        'columns' => [

            [
                'attribute' => 'date_time_game',
                'content' => function($model) {
                    return '<strong>'.date('d.m.y H:i', $model->dtime).'</strong>';
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
                'content' => function($model) use ($team){
                    return
                        ($model->home_participant_id == $team->id) ?
                            Html::img(Teams::getPath().'/'.$model->home_logo, ['width' => 30]).' '.'<strong>'.$model->home_team.'</strong>' :
                            Html::img(Teams::getPath().'/'.$model->home_logo, ['width' => 30]).' '.$model->home_team;
                },
                'options' => [
                    'class' => 'col-xs-3 col-sm-4'
                ],
                'hAlign' => 'right',
                'vAlign' => 'middle',
            ],
            [
                'content' => function($model) {
                    return
                        "<strong>".$model->home_score.' : '.$model->guest_score."</strong>"
                        ;
                },
                'options' => [
                    'class' => 'col-xs-4 col-sm-2'
                ],
                'hAlign' => 'center',
                'vAlign' => 'middle',
            ],

            [
                'content' => function($model) use($team) {
                    return
                        ($model->guest_participant_id == $team->id) ?
                            Html::img(Teams::getPath().'/'.$model->guest_logo, ['width' => 30]).' '.'<strong>'.$model->guest_team.'</strong>' :
                            Html::img(Teams::getPath().'/'.$model->guest_logo, ['width' => 30]).' '.$model->guest_team;
                },
                'options' => [
                    'class' => 'col-xs-4'
                ],
                'hAlign' => 'left',
                'vAlign' => 'middle',
            ],
        ]
    ]);?>
        </div>
    </div>
</div>
