<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\users\users */

$this->title = 'Редактировать пользователя: ' . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->username;
?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 users-update">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
            'allRoles' => $allRoles,
            'tournaments' => $tournaments,
        ]) ?>
    </div>
</div>
