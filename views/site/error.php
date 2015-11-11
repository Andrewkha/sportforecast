<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = 'Ошибка!';
?>
<div class="site-error">
    <div class="row">
        <div class="col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 site-login">
            <h1><?= Html::encode($this->title) ?></h1>

            <div class="alert alert-danger">
                <?= nl2br(Html::encode($message)) ?>
            </div>

            <p>
                Упс... Ошибка. Пожалуйста, сообщите администрации сайта <?= Html::mailto(Yii::$app->params['adminEmail']);?>
            </p>
        </div>
    </div>

</div>
