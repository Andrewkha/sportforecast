<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\rbac\ManagerInterface;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\users\users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">

        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'username')->textInput(['maxlength' => 100]) ?>

            <?= $form->field($model, 'email')->textInput(['maxlength' => 100]) ?>

            <?= $form->field($model, 'created_on')->textInput(['value' => date('d/m/Y',$model->created_on), 'disabled' => true]) ?>

            <?= $form->field($model, 'last_login')->textInput(['value' => date('d/m/Y H:i',$model->last_login), 'disabled' => true]) ?>

            <?= $form->field($model, 'updated_on')->textInput(['value' => date('d/m/Y H:i',$model->updated_on), 'disabled' => true]) ?>

            <div class="row">
                <?= $form->field($model, 'active', [
                    'options' => [
                        'class' => 'col-xs-8'
                    ]
                ])->dropDownList($model::getStatuses()) ?>
            </div>

            <?= $form->field($model, 'first_name')->textInput(['maxlength' => 50]) ?>

            <?= $form->field($model, 'last_name')->textInput(['maxlength' => 50]) ?>

            <div class = 'form-group field-users-avatar'>
                <label class="control-label" for="users-avatar">Аватар</label>
                <?= Html::img($model->fileUrl, ['id'=>'users-avatar', 'width' => 150]) ?>
            </div>

            <div class="row">
                <?= $form->field($model, 'notifications',[
                    'options' => [
                        'class' => 'col-xs-8'
                    ]
                ])->dropDownList($model::getSubscription()) ?>
            </div>

        <div class = 'form-group field-users-roles'>
            <label class="control-label" for="users-roles">Роли</label>
                <?php foreach($allRoles as $role): ?>
                    <?php $rbac = \Yii::$app->authManager;?>
                    <?= Html::checkbox("role[$role->name]", $rbac->checkAccess($model->id, $role->name), ['uncheck' => 0, 'label' => $role->name, 'disabled' => ($role->name == 'user')? true : false])?>
                <?php endforeach;?>

        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
<hr>
<h2>Статистика участия</h2>
<div class="row">

    <?= GridView::widget([
        'dataProvider' => $tournaments,
        'options' => [
            'class' => 'col-xs-12 col-md-10'
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'options' => [
                    'class' => 'col-xs-1',
                ],
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
            ],

            [
                'attribute' => 'tournament_name',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'filter' => false,
                'options' => [
                    'class' => 'col-xs-3',
                ],
            ],

            [
                'attribute' => 'country0.country',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'filter' => false,
                'options' => [
                    'class' => 'col-xs-2',
                ],
            ],

            [
                'attribute' => 'status',
                'header' => 'Статус',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'filter' => false,
                'options' => [
                    'class' => 'col-xs-2',
                ],
            ],

            [
                'header' => 'Место',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'content' => function($model1) use($model) {
                    return (\app\models\users\UsersTournaments::find()->findModel($model1->id_tournament,$model->id)->one()->position);
                },
                'filter' => false,
                'options' => [
                    'class' => 'col-xs-1',
                ],
            ],

            [
                'attribute' => 'userPoints',
                'header' => 'Очки',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'content' => function($model1) use ($model) {
                    return (\app\models\users\UsersTournaments::find()->findModel($model1->id_tournament,$model->id)->one()->totalPoints);
                },
                'filter' => false,
                'options' => [
                    'class' => 'col-xs-1',
                ],
            ],

            [
                'attribute' => 'notification',
                'header' => 'Уведомления',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'content' => function($model1) use ($model) {
                    return (\app\models\users\UsersTournaments::find()->findModel($model1->id_tournament,$model->id)->one()->getSubscriptionStatus());
                },
                'filter' => false,
                'options' => [
                    'class' => 'col-xs-1',
                ],
            ],
        ]
    ]);
    ?>

</div>
