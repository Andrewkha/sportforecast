<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use letyii\tinymce;

/* @var $this yii\web\View */
/* @var $model app\models\news\news */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class = "col-xs-12 col-sm-10">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'subject', [
            'template' => '{label} <div class="row"><div class="col-xs-10 col-sm-6 col-lg-4">{input}{error}{hint}</div></div>'
        ])->textInput(['maxlength' => 1024]) ?>

        <?= $form->field($model, 'body')->widget(tinymce\Tinymce::className(),[
            'configs'=> [
                'plugins' => 'link',
                'menu' => [],
            ]
        ]) ?>

        <?= Html::checkbox('send', true, ['label' => 'Отправить пользователям']);?>

        <?= $form->field($model, 'id_tournament', [
            'template' => '{label} <div class="row"><div class="col-xs-8 col-sm-5 col-md-3">{input}{error}{hint}</div></div>'
        ])->dropDownList($tournaments) ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>