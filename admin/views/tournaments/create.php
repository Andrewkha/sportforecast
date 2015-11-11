<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\tournaments\tournaments */

$this->title = Yii::t('app', 'Create', [
    'modelClass' => 'Tournaments',
]);
$this->params['breadcrumbs'][] = ['label' => 'Справочники', 'url' => ['/admin']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tournaments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="col-lg-offset-1 col-lg-10 tournaments-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'scenario' => 'create'
    ]) ?>

</div>
