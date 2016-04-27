<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 4/26/2016
 * Time: 1:45 PM
 */
use yii\bootstrap\ActiveForm;
use app\models\tournaments\TeamTournaments;
use yii\helpers\Html;

?>
<div class = 'col-xs-12 col-md-5 col-lg-4 col-lg-offset-1'>
    <p class = 'text-center' style="font-size:1.5em; color: #777">Прогноз на призеров турнира</p>

    <?php $teams = TeamTournaments::find()
        ->select(['{{%teams}}.team_name', 'id'])
        ->where(['id_tournament' => $tournament->id_tournament])
        ->joinWith('idTeam')
        ->indexBy('id')
        ->column()
    ;?>

    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($winners, 'first')->dropDownList($teams, ['prompt' => '---Выберете команду---']);?>
        <?= $form->field($winners, 'second')->dropDownList($teams, ['prompt' => '---Выберете команду---']);?>
        <?= $form->field($winners, 'third')->dropDownList($teams, ['prompt' => '---Выберете команду---']);?>

        <?php //todo add disable for fields and button ?>
        <?= Html::submitButton('Сохранить', ['type' => 'button', 'class' => ['btn btn-primary pull-right']]);?>
    <?php ActiveForm::end();?>
    <br>
    <br>
    <br>
    <br>
</div>