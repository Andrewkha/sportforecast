<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\models\countries\Countries;
use app\models\tournaments\Tournaments;
use app\components\grid\extendedGridView;
use kartik\widgets\DatePicker;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\tournaments\tournaments */
/* @var $form yii\widgets\ActiveForm */
?>

<div class = "row">
    <div class="col-xs-8 col-sm-6 col-md-4 col-lg-3">
        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'tournament_name',[
            //    'template' => '{label} <div class="row"><div class="col-sm-4">{input}{error}{hint}</div></div>'
            ])->textInput(['maxlength' => 255]) ?>

            <?= $form->field($model, 'country',[
                'template' => '{label} <div class="row"><div class="col-xs-10">{input}{error}{hint}</div></div>'
            ])->dropDownList(ArrayHelper::map(Countries::find()->orderBy('country', 'asc')->all(), 'id', 'country'),
                    ['prompt' => '---Выберете страну---']) ?>

            <?= $form->field($model, 'num_tours', [
                'template' => '{label} <div class="row"><div class="col-xs-5 col-sm-3">{input}{error}{hint}</div></div>'
            ])->textInput() ?>

            <?= $form->field($model, 'startsOn', [
                'template' => '{label} <div class="row"><div class="col-xs-12 col-sm-8">{input}{error}{hint}</div></div>'
            ])->widget(DatePicker::className(),[
                    'removeButton' => false,
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                    ],
                    'options' => [
                        'value' => (isset($model->startsOn))? date('d.m.Y', $model->startsOn) : '',
                    ]
            ]) ?>

            <?= $form->field($model, 'is_active', [
               'template' => '{label} <div class="row"><div class="col-xs-8">{input}{error}{hint}</div></div>'
            ])->dropDownList(
                $model::statuses(), ['prompt' => '---Статус---']
            ) ?>

            <?= $form->field($model, 'enableAutoprocess')->checkbox();?>

            <?= $form->field($model, 'autoProcessURL')->input('text');?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Сохранить'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::a('Отмена', 'index', ['class' => 'btn btn-default']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
<hr>
<?php if($scenario != 'create'): ?>


    <h3>Все прогнозисты турнира</h3>

    <?php
        Modal::begin([
            'header' => '<h4>Детали прогноза</h4>',
            'id' => 'modal',
            'size' => 'modal-md',
        ]);
        echo "<div id = 'modalContent'></div>";
        Modal::end();
    ?>

    <?php
    Modal::begin([
        'header' => '<h4>Прогноз по турам</h4>',
        'id' => 'mU',
        'size' => 'modal-sm',
    ]);
    echo "<div id = 'modalUserContent'></div>";
    Modal::end();
    ?>

    <div class="row">
        <?= extendedGridView::widget([
            'dataProvider' => $forecasters,
            'options' => [
                'class' => 'col-xs-12 col-md-10 col-lg-10',
            ],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'contentOptions' => [
                        'align' => 'center',
                        'style' => 'vertical-align:middle',
                    ],
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'options' => [
                        'class' => 'col-xs-1',
                    ],
                    'header' => 'Место',
                ],

                [
                    'header' => 'Пользователь',
                    'vAlign' => 'middle',
                    'options' => [
                        'class' => 'col-xs-3 col-sm-2',
                    ],
                    'hAlign' => 'left',
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'content' => function($model1) use ($model) {
                        return Html::button($model1['idUser']['username'], [
                            'value' => Url::to(['tournaments/user', 'user' => $model1['id_user'], 'tournament' => $model->id_tournament]),
                            'class' => 'btn btn-link modalUser']);
                    }
                ],

                [
                    'header' => 'Очки',
                    'attribute' => "points",
                    'vAlign' => 'middle',
                    'options' => [
                        'class' => 'col-xs-1',
                    ],
                    'hAlign' => 'center',
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                ],

                [
                    'header' => 'Прогнозы по турам',
                    'attribute' => "tours",
                    'vAlign' => 'middle',
                    'options' => [
                        'class' => 'col-xs-7 col-sm-8',
                    ],
                    'hAlign' => 'center',
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'value' => function($model1) use ($model){
                        $string = '';
                        foreach($model1['tours'] as $k => $one) {
                            $options['value'] = Url::to(['tournaments/tour', 'tour' => $k, 'tournament' => $model->id_tournament, 'user' => $model1['id_user']]);
                            switch ($one) {
                                case 0:
                                    $options['class'] = 'btn btn-danger modalList';
                                    break;

                                case 1:
                                    $options['class'] = 'btn btn-warning modalList';
                                    break;

                                case 2:
                                    $options['class'] = 'btn btn-success modalList';
                                    break;
                            }
                            $k = Html::button($k, $options);
                            $string .= $k;
                        }

                        return $string;
                    },
                    'format' => 'raw',
                ],
            ]
        ]);?>
    </div>

    <?php
        if($model->is_active != Tournaments::FINISHED && $nextTour) :
    ?>
            <h4>Следующий тур в турнире <?= $nextTour;?></h4>

            <?= Html::beginForm(['tournaments/reminder'], 'post');?>
                <?= Html::hiddenInput('tournament', $model->id_tournament);?>
                <?= Html::hiddenInput('tour', $nextTour);?>
                <?= Html::submitButton("Отправить напоминания на $nextTour тур", ['class' => 'btn btn-success',]);?>
            <?= Html::endForm();?>

    <?php    endif; ?>

<?php endif;?>
<p></p>