<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Alert;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Вход на сайт';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 site-login">
        <h1><?= Html::encode($this->title) ?></h1>

        <p>Введите логин и пароль/<?= Html::a('Восстановление забытого пароля', ['site/passreset']);?></p>

        <div class = "row">
            <div class="col-xs-8 col-sm-6 col-md-4 col-lg-3">
                <?php $form = ActiveForm::begin([
                    'id' => 'login-form',
                    'fieldConfig' => [
                        'template' => "{label}\n{input}\n{error}",
                    ],
                ]); ?>

                <?= $form->field($model, 'username', [
                    'options' => [
                        'class' => 'form-group',
                    ]
                ]) ?>

                <?= $form->field($model, 'password', [
                    'options' => [
                        'class' => 'form-group',
                    ]
                ])->passwordInput() ?>

                <?= $form->field($model, 'rememberMe', [
                    'template' => "{input}\n{error}",
                ])->checkbox() ?>

                <div class="form-group">
                    <?= Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
