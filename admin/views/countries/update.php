<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\countries\countries */

$this->title = Yii::t('app', 'Редактирование страны: ', [
    'modelClass' => 'Countries',
]) . ' ' . $model->country;
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Страны'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Изменить');
?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 countries-update">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
</div>
