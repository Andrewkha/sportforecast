<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Ваш профиль';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class = "row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 profile">
        <h1 class = 'text-center'><?= Html::encode($this->title) ?></h1>

        <div class = 'row'>
            <div class="col-xs-6 col-xs-offset-3 alert alert-info text-center">
                <p><?= $model->username;?> здесь Вы можете отредактировать свой профиль</p>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-11 col-sm-7 col-md-5 col-lg-4">
                <?php $form = ActiveForm::begin([
                    'id' => 'profile-form',
                    'options' => [
                        'enctype' => 'multipart/form-data'
                    ]
                ]); ?>

                    <?= $form->field($model, 'username', [
                            'options' => [
                                'class' => 'form-group has-feedback',
                            ],
                        ])->input('text', [
                                'placeholder' => 'Логин'
                            ])->label(false) ?>

                    <?= $form->field($model, 'email', [
                        'options' => [
                            'class' => 'form-group has-feedback',
                        ],
                        'template' => "<div class = 'input-group'><span class='input-group-addon'>@</span>{input}</div>\n{hint}\n{error}"
                    ])->input('text', [
                            'placeholder' => 'Email'
                    ]) ?>

                    <?= $form->field($model, 'first_name', [
                        'options' => [
                            'class' => 'form-group has-feedback',
                        ]
                    ])->input('text', [
                            'placeholder' => 'Имя'
                        ])->label(false) ?>

                    <?= $form->field($model, 'last_name', [
                        'options' => [
                            'class' => 'form-group has-feedback',
                        ]
                    ])->input('text', [
                            'placeholder' => 'Фамилия'
                        ])->label(false) ?>

                    <?= $form->field($model, 'notifications')->checkbox();?>

                    <div class = 'form-group field-old_avatar'>
                        <div class = "row">
                            <div class="col-xs-12">
                                <label class="control-label" for="old_avatar">Ваш аватар</label>
                            </div>
                        </div>
                        <?= Html::img($model->fileUrl, ['id'=>'old_avatar', 'width' => 100]) ?>
                    </div>

                    <?= $form->field($model, 'avatar')->widget(FileInput::className(),[
                            'options' => ['accept' => 'image/*'],
                            'pluginOptions' => [
                                'showRemove' => false,
                                'showUpload' => false
                            ]
                    ]) ?>

                    <h3>Смена пароля</h3>

                    <?= $form->field($model, 'password', [
                        'options' => [
                            'class' => 'form-group has-feedback',
                        ]
                    ])
                        ->passwordInput([
                            'placeholder' => 'Новый пароль'
                        ])
                        ->label(false) ?>

                    <?= $form->field($model, 'password_repeat', [
                        'options' => [
                            'class' => 'form-group has-feedback',
                        ]
                    ])
                        ->passwordInput([
                            'placeholder' => 'Повторите пароль'
                        ])
                        ->label(false) ?>

                    <div class="form-group">
                        <?= Html::submitButton('Сохранить',
                            [
                                'class' => 'btn btn-primary',
                                'name' => 'contact-button'
                        ]) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
