<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 3/2/15
 * Time: 6:25 PM
 * To change this template use File | Settings | File Templates.
 */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use app\models\tournaments\Tournaments;

?>

<?php $form = ActiveForm::begin([
    'action' => 'add-participants'
])?>

<?php if($model->is_active == Tournaments::NOT_STARTED) : ?>
    <div class="form-group">
        <?= Html::dropDownList('candidates', null, ArrayHelper::map($teams, 'id_team', 'team_name', 'country0.country'), ['class' => 'form-control', 'multiple' => 'multiple', 'size' => 5, 'style' => 'width:250px']);?>
    </div>
    <?= Html::hiddenInput('tournament', $tournament);?>
    <div class="form-group ">
        <?= Html::submitButton('Добавить', ['class' => 'btn btn-primary']) ?>
    </div>

<?php endif;?>

<?php ActiveForm::end(); ?>

<div class = "row">
    <?= GridView::widget([
        'dataProvider' => $participants,
        'options' => [
            'class' => 'col-xs-12 col-md-10 col-lg-8'
        ],
        'columns' =>  [
            [
                'header' => 'Место',
                'class' => 'yii\grid\SerialColumn',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'options' => [
                    'class' => 'col-sm-1'
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
            ],

            [
                'attribute' => 'id',
                'header' => 'ID участника',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'options' => [
                    'class' => 'col-sm-1'
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
            ],

            [
                'header' => 'Команда',
                'content' => function($model) {
                    return Html::a($model['team_name'], ["teams/update", 'id' => $model['id_team']]);
                },
                'contentOptions' => [
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'options' => [
                    'class' => 'col-sm-3'
                ],
            ],

            [
                'header' => 'Сыграно матчей',
                'attribute' => 'games_played',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'options' => [
                    'class' => 'col-sm-1'
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'content' => function ($model) {
                    return (isset($model['games_played']))? $model['games_played'] : 0;
                }
            ],

            [
                'header' => 'Очки',
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'options' => [
                    'class' => 'col-sm-1'
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'content' => function ($model) {
                    return (isset($model['pts']))? $model['pts'] : 0;
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'options' => [
                    'class' => 'col-sm-1'
                ],
                'contentOptions' => [
                    'align' => 'center',
                    'style' => 'vertical-align:middle',
                ],
                'headerOptions' => [
                    'style' => 'text-align:center',
                ],
                'header' => 'Удалить участника',
                'urlCreator' => function($action, $model, $key, $index) {
                    return "/admin/tournaments/delete-participant?id=".$model['id'];
                }
            ]
        ]
    ]);?>
</div>