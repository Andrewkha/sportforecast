<?php

//use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\DataColumn;
use kartik\widgets\Alert;
use kartik\helpers\Html;

$this->title = Yii::t('app', 'Админка');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 refbooks-index">


        <?= Html::ul($items,['item' => function($item, $index){
            return Html::a($item, ["$index/"]);
        }]);?>

    </div>
</div>