<?php

use yii\helpers\Html;
use kartik\widgets\Alert;


/* @var $this yii\web\View */
/* @var $model app\models\news\news */

$this->title = 'Добавить новость';
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if(Yii::$app->session->hasFlash('error')) {

    echo Alert::widget([
        'type' => Alert::TYPE_DANGER,
        'title' => 'Ошибка добавления',
        'icon' => 'glyphicon glyphicon-remove-sign',
        'body' => Yii::$app->session->getFlash('error'),
        'showSeparator' => true,
        'delay' => 4000
    ]);
}

?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 news-create">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
            'tournaments' => $tournaments,
        ]) ?>
    </div>
</div>
