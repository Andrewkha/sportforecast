<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\teams\teams */

$this->title = Yii::t('app', 'Редактировать: ', [
    'modelClass' => 'Teams',
]) . ' ' . $model->team_name;
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Команды'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Изменить');
?>
<div class="col-lg-offset-1 col-lg-10 teams-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
