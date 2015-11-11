<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Alert;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Восстановление пароля';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php

if(Yii::$app->session->hasFlash('error')) {

    echo Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => 'Произошла ошибка',
        'icon' => 'glyphicon glyphicon-remove-sign',
        'body' => Yii::$app->session->getFlash('error'),
        'showSeparator' => true,
        'delay' => 5000
    ]);
}

if(Yii::$app->session->hasFlash('success')) {

    echo Alert::widget([
        'type' => Alert::TYPE_SUCCESS,
        'title' => 'Проверьте почту',
        'icon' => 'glyphicon glyphicon-remove-sign',
        'body' => Yii::$app->session->getFlash('success'),
        'showSeparator' => true,
        'delay' => 5000
    ]);
}
?>

<div class = "row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 passreset">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>Введите логин и email для идетнификации пользователя. Инструкции будут высланы Вам на email</p>
        <div class = "row">
            <div class = "col-xs-8 col-sm-6 col-md-4 col-lg-3">
                <?php $form = ActiveForm::begin([
                    'id' => 'passreset-form',
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}",
                    ],
                ]); ?>

                <?= $form->field($model, 'username') ?>

                <?= $form->field($model, 'email')?>

                <div class="form-group">
                        <?= Html::submitButton('Сбросить', ['class' => 'btn btn-primary', 'name' => 'passreset-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
