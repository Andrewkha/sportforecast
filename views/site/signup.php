<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */

$this->title = 'Регистрация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 site-singup">
        <h1 class = 'text-center'><?= Html::encode($this->title) ?></h1>

        <div class = 'row'>
            <div class="alert alert-info col-xs-6 col-xs-offset-3 text-center">
                Добро пожаловать! Введите регистрационные данные
            </div>
        </div>

        <div class="row">
            <div class="col-xs-11 col-sm-7 col-md-5 col-lg-4">
            <?php $form = ActiveForm::begin([
                'id' => 'signup-form',
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

                <?= $form->field($model, 'password', [
                    'options' => [
                        'class' => 'form-group has-feedback',
                    ]
                ])
                    ->passwordInput([
                        'placeholder' => 'Пароль'
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

                <?= $form->field($model, 'avatar', [
                    'options' => [
                        'class' => 'form-group',
                    ]

                ])->widget(FileInput::className(),[
                        'options' => ['accept' => 'image/*'],
                        'pluginOptions' => [
                            'showRemove' => false,
                            'showUpload' => false
                        ]
                    ]) ?>

                <?= $form->field($model, 'verifyCode', [
                    'options' => [
                        'class' => 'form-group',
                    ]
                ])->widget(Captcha::className(), [
                    'template' => '<div class="row"><div class="col-md-5">{image}</div><div class="col-md-7">{input}</div></div>',
                ]) ?>
                <div class="form-group">
                    <p>
                        <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                    </p>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
        </div>
    </div>
</div>
