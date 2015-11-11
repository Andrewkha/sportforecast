<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 7/2/15
 * Time: 6:01 PM
 * To change this template use File | Settings | File Templates.
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Назначение псевдонимов автопроцессинга';
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tournaments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $trn->tournament_name, 'url' => ['tournaments/update', 'id' => $trn->id_tournament]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 tournaments-alias">
        <h3>Укажите псевдонимы команд, как они обозначены в источнике данных</h3>

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'horizontalCssClasses' => [
                    'label' => 'col-xs-8 col-sm-3',
                    'wrapper' => 'col-xs-7 col-md-5 col-lg-3',
                    'offset' => ''
                ]
            ]
        ]);?>
                <?php foreach($teams as $k => $team) :?>
                    <?= $form->field($team, "[$k]alias")->input('text', [
                            'placeholder' => "Псевдоним команды {$team->idTeam->team_name}",
                        ])->label($team->idTeam->team_name);?>
                <?php endforeach;?>
                <?= Html::submitButton('Сохранить',['class' => 'btn btn-info align-right col-xs-offset-5 col-sm-offset-8 col-md-offset-7 col-lg-offset-5']);?>
        <?php ActiveForm::end();?>
    </div>
</div>