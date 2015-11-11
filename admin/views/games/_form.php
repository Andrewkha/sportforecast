<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
//use kartik\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\games\Games */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-xs-8 col-sm-6 col-md-4 col-lg-3">
        <?php $form = ActiveForm::begin([
            'action' => ['games/add'],
        ]); ?>

        <?= $form->field($model, 'id_team_home', [
            'template' => '{label}{input}{error}{hint}'
        ])->dropDownList(ArrayHelper::map($teams, 'id', 'idTeam.team_name'), ['prompt' => 'Выберете команду'])->label('Команда хозяев') ?>

        <?= $form->field($model, 'id_team_guest', [
            'template' => '{label}{input}{error}{hint}'
        ])->dropDownList(ArrayHelper::map($teams, 'id', 'idTeam.team_name'), ['prompt' => 'Выберете команду'])->label('Команда гостей') ?>

        <?= $form->field($model, 'tour', [
            'template' => '{label}{input}{error}{hint}'
        ])->input('number', ['style' => 'width:3em; -moz-appearance: textfield;']) ?>

        <div class="row">
            <?= $form->field($model, 'date_time_game', [
            'template' => '{label}{input}{error}{hint}',
            'options' => [
                'class' => 'col-xs-10'
            ]
        ])->input('text', ['class' => 'datepicker form-control']);
            ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Добавить', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
