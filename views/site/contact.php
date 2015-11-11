<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Контакты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <div class="row">
        <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 site-contact">
            <h1><?= Html::encode($this->title) ?></h1>

            <?php if (Yii::$app->session->hasFlash('contactFormSubmitted')): ?>

            <div class="alert alert-success">
                Спасибо за обращение. Свяжусь с вами в ближайшее время.
            </div>

            <?php else: ?>

            <p>
                По замечаниям и предложениям, воспользуйтесь данной формой обратной связи
            </p>

            <div class="row">
                <div class="col-lg-5">
                    <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
                        <?= $form->field($model, 'name') ?>
                        <?= $form->field($model, 'email') ?>
                        <?= $form->field($model, 'subject') ?>
                        <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
                        <?= $form->field($model, 'verifyCode')->widget(Captcha::className(), [
                            'template' => '<div class="row"><div class="col-lg-3">{image}</div><div class="col-lg-6">{input}</div></div>',
                        ]) ?>
                        <div class="form-group">
                            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <?php endif; ?>
        </div>
    </div>
</div>
