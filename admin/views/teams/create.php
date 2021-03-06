<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\teams\teams */

$this->title = Yii::t('app', 'Создать команду', [
    'modelClass' => 'Teams',
]);
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Команды'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 teams-create">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
</div>
