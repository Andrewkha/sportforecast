<?php

use yii\helpers\Html;
use kartik\widgets\Alert;
use app\assets\ModalAsset;

/* @var $this yii\web\View */
/* @var $model app\models\tournaments\tournaments */

ModalAsset::register($this);

$this->title = Yii::t('app', 'Update: ', [
    'modelClass' => 'Tournaments',
]) . ' ' . $model->tournament_name;
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tournaments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->tournament_name;

if(Yii::$app->session->hasFlash('error')) {

    echo Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => 'Ошибка удаления записи',
        'icon' => 'glyphicon glyphicon-remove-sign',
        'body' => Yii::$app->session->getFlash('error'),
        'showSeparator' => true,
        'delay' => 2000
    ]);
}

if(Yii::$app->session->hasFlash('success')) {

    echo Alert::widget([
        'type' => Alert::TYPE_SUCCESS,
        'icon' => 'glyphicon glyphicon-ok-sign',
        'body' => Yii::$app->session->getFlash('success'),
        'showSeparator' => true,
        'delay' => 2000
    ]);
}
?>

<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 tournaments-update">

        <h1><?= Html::encode($this->title) ?></h1>

        <h3><?= Html::a('Игры турнира', ['games/index', 'tournament' => $model->id_tournament])?></h3>
        <?php if($model->enableAutoprocess === 1) :?>
            <h3><?= Html::a('Псевдонимы автопроцессинга', ["tournaments/alias?tournament=$model->id_tournament"])?></h3>
        <?php endif;?>

        <hr>

        <?= $this->render('_teams', [
            'participants' => $participants,
            'teams' => $teams,
            'tournament' => $model->id_tournament,
            'model' => $model,
        ]) ?>

        <?= $this->render('_form', [
            'model' => $model,
            'nextTour' => $nextTour,
            'forecasters' => $forecasters,
            'scenario' => 'update'
        ]) ?>

    </div>
</div>