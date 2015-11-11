<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\tournaments\TournamentsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Новости';

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10">

        <h2>Новости</h2>

        <?php $form = ActiveForm::begin([
            'type' => ActiveForm::TYPE_INLINE,
        ]);?>
        <div class="row">
                <div class="form-group col-xs-8 col-sm-10 col-md-8 col-lg-5">
                    <?= Html::dropDownList('tournamentFilter', $selected, $tournamentFilter, ['prompt' => '---Фильтр новостей---', 'class' => 'form-control news-dropdown']);?>
                    <?= Html::submitButton('Выбрать', ['class' => 'btn btn-success form-control col-sm-offset-1']);?>
                </div>

        </div>
        <?php ActiveForm::end();?>

        <?php foreach($news as $one) :?>
            <div class="row">
                <div class="panel panel-primary col-lg-7 news-list">
                    <div class="panel-heading">
                        <h3 class = "panel-title"><?= ($one->id_tournament == 0)? 'Новости сайта' : $one->tournament->tournament_name;?> - <?=$one->subject;?></h3>
                    </div>
                    <div class="panel-body">
                        <?= $one->body;?>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-xs-7">
                                <p>Разместил: <?= $one->author0->username;?></p>
                            </div>
                            <div class="col-xs-4">
                                <p class="pull-right"><?= date('d.m.Y H:i', $one->date);?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach;?>

        <?= LinkPager::widget([
                'pagination' => $pages,
        ]);?>
    </div>
</div>
