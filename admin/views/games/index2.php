<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 3/12/15
 * Time: 11:36 AM
 * To change this template use File | Settings | File Templates.
 */

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use app\assets\TimePickerAsset;
use kartik\widgets\Growl;
use app\models\tournaments\Tournaments;
use app\components\grid\extendedGridView;

TimePickerAsset::register($this);

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', "Игры турнира $curr_tournament->tournament_name");
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tournaments'), 'url' => ['tournaments/index']];
$this->params['breadcrumbs'][] = ['label' => $curr_tournament->tournament_name, 'url' => ['/admin/tournaments/update', 'id' => $curr_tournament->id_tournament]];
$this->params['breadcrumbs'][] = 'Игры турнира';

?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 games-index">

        <?php
            if(Yii::$app->session->hasFlash('status')) {

                echo Growl::widget([
                    'type' => Growl::TYPE_SUCCESS,
                    'icon' => 'glyphicon glyphicon-ok-sign',
                    'body' => Yii::$app->session->getFlash('status'),
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

        <h1><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(['action' => ''])?>

            <div class="form-group">
                <?= Html::dropDownList('tours', null, $tour_list, ['class' => 'form-control', 'multiple' => 'multiple', 'size' => 5, 'style' => 'width:100px']);?>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Выбрать', ['class' => 'btn btn-primary']) ?>
            </div>

        <?php ActiveForm::end(); ?>

        <?php if($curr_tournament->enableAutoprocess == 1 && $curr_tournament->autoProcessURL != NULL) :?>

            <?= Html::a('Загрузка календаря и результатов', ['tournaments/autoprocess', 'id' => $curr_tournament->id_tournament], ['class' => 'btn btn-info']);?>
        <?php endif;?>

        <?php foreach($tourGames as $tour=> $games):?>
            <br>

            <?php $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_INLINE,
                'fieldConfig' => [
                    'autoPlaceholder' => false,
                ]
            ]);?>
                <?= Html::input('hidden', 'tour', $tour);?>

                <div class = 'row'>
                    <?= extendedGridView::widget([
                        'dataProvider' => $tourGames[$tour],
                        'caption' => "Тур $tour",
                        'hover' => true,
                        'options' => [
                            'class' => 'col-xs-12 col-md-10 col-lg-8',
                        ],
                        'summary' => '',
                        'columns' => [

                            [
                                'attribute' => "id_game",
                                'vAlign' => 'middle',
                                'options' => [
                                    'class' => 'col-xs-1',
                                ],
                                'hAlign' => 'center',
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'footer' => '33',
                            ],

                            [
                                'header' => 'Участники',
                                'vAlign' => 'middle',
                                'content' => function($model) use($form) {
                                    return

                                        "<row>".
                                            "<div class = 'text-right col-xs-4'>".
                                                $model->idTeamHome->idTeam->team_name.
                                                " ".
                                            "</div>".
                                            "<div class = 'text-center col-xs-4'>".
                                                Html::input('number', "Games[$model->id_game][score_home]", $model->score_home,[
                                                    'class' => 'score',
                                                ]).
                                                Html::input('number', "Games[$model->id_game][score_guest]", $model->score_guest,[
                                                    'class' => 'score',
                                                ]).
                                            "</div>".
                                            "<div class = 'text-left col-xs-4'>".
                                                " ".$model->idTeamGuest->idTeam->team_name.$form->field($model, "[$model->id_game]id_game")->hiddenInput().
                                            "</div>".
                                        "</row>".
                                        "<row>".
                                            "<div class = 'col-xs-12 text-center'>".


                                            "</div>".
                                        "</row>"
                                        ;

                                },
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-8',
                                ],
                                'hAlign' => 'center',
                            ],

                            [
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'hAlign' => 'center',
                                'vAlign' => 'middle',
                                'attribute' => 'date_time_game',
                                'options' => [
                                    'class' => 'col-xs-2',
                                ],
                                'content' => function($model) use ($form){
                                    return $form->field($model,"[$model->id_game]date_time_game")->input('text', ['class' => 'datepicker', 'value' => date('d.m.y H:i', $model->date_time_game)]);
                                }
                            ],

                            [
                                'class' => '\kartik\grid\ActionColumn',
                                'deleteOptions' => ['label' => '<i class="glyphicon glyphicon-remove"></i>'],
                                'template' => '{delete}',
                                'header' => false,
                                'hAlign' => 'center',
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                            ]
                        ]
                    ]);?>
                </div>

                <div class = 'row'>
                    <div class = 'col-sm-8 kv-align-center'>
                        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']);?>
                    </div>
                </div>

            <?php ActiveForm::end();?>
        <?php endforeach;?>

        <hr>


        <?php if($curr_tournament->is_active != Tournaments::FINISHED): ?>

            <h1>Добавить игру</h1>

            <?= $this->render('_form', [
                'model' => $newGame,
                'teams' => $participants,
                'tournament' => $curr_tournament->id_tournament,
            ]) ?>

            <h1>Импортировать из Excel</h1>

            <?php $uploadForm = ActiveForm::begin([
                'action' => 'upload',
                'options' => [
                    'enctype' => 'multipart/form-data',
                ]
            ]);    ?>
            <?= $uploadForm->field($file, 'file')->fileInput([
                'accept' => 'application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel.12'
            ])->label('Выберете файл');?>
            <?= Html::submitButton('Загрузить', ['class' => 'btn btn-primary']);?>

            <?php ActiveForm::end();?>
        <?php endif;?>
    </div>
</div>

<script>
    $(function() {

        $( ".datepicker" ).datetimepicker({
            controlType: 'select',
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd.mm.y',
            stepMinute: 15,
            timeFormat: "HH:mm"
        });

    });
</script>