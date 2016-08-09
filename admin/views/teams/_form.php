<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use app\models\countries\Countries;

/* @var $this yii\web\View */
/* @var $model app\models\teams\teams */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">

    <?php $form = ActiveForm::begin([
        'options' => [
            'enctype' => 'multipart/form-data',
            'class' => 'col-xs-8 col-sm-6 col-md-4 col-lg-3'
        ]
    ]); ?>

    <?= $form->field($model, 'team_name')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'country')->dropDownList(
        ArrayHelper::map(Countries::find()->orderBy('country', 'asc')->all(), 'id', 'country'),
        ['prompt' => '---Выберите страну---']
    ); ?>

    <?php
        if($model->isNewRecord) :
            echo $form->field($model, 'team_logo')->label('Логотип')->fileInput();
        else:
    ?>

            <div class = 'form-group field-teams-team_logo'>
                <label class="control-label" for="logo">Логотип</label>
                <?= Html::img($model->fileUrl, ['id'=>'logo', 'width' => 100]) ?>
            </div>

            <?= $form->field($model, 'team_logo')->label('Изменить')->fileInput();?>

    <?php endif;?>

    <div class="form-group ">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Создать') : Yii::t('app', 'Сохранить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Отмена', 'index', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
