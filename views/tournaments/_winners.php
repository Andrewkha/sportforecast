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

<?php
if(time() > $tournament->wfDueTo)
    $expired = true;
else
    $expired = false;

$subtitle = ($expired)? 'Прием прогнозов на призеров окончен '.date('d.m.y', $tournament->wfDueTo) :
    'Вы можете сделать прогноз на призеров турнира до '.date('d.m.y', $tournament->wfDueTo). ' и заработать дополнительные очки';
?>
<div class = 'col-xs-12 col-md-5 col-lg-4 col-lg-offset-1'>
    <p class = 'text-center' style="font-size:1.5em; color: #777">Прогноз на призеров турнира</p>
    <p class = 'text-center' style="color: #777"><?=$subtitle;?></p>

    <?php $teams = TeamTournaments::find()
        ->select(['{{%teams}}.team_name', 'id'])
        ->where(['id_tournament' => $tournament->id_tournament])
        ->joinWith('idTeam')
        ->indexBy('id')
        ->column()
    ;?>

    <?php
        $options = ['prompt' => '---Выберете команду---'];
        if($expired)
            $options['disabled'] = 'disabled';
    ?>
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($winners, 'first')->dropDownList($teams, $options);?>
        <?= $form->field($winners, 'second')->dropDownList($teams, $options);?>
        <?= $form->field($winners, 'third')->dropDownList($teams, $options);?>

        <?= (!$expired)? Html::submitButton('Сохранить', ['type' => 'button', 'class' => ['btn btn-primary pull-right']]) : NULL;?>
    <?php ActiveForm::end();?>
    <br>
    <?php if($tournament->is_active == \app\models\tournaments\Tournaments::FINISHED) :?>
        <p><b>Всего дополнительных очков: <?= $totalAdditionalPoints;?></b></p>
        <p><?= $additionalPoints;?></p>
    <?php endif;?>
    <br>
</div>