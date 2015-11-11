<?php
use yii\helpers\Html;

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->forgotten_password_code]);
?>
<div class="password-reset">
    <div class = "row"></div>
        <h3>Добрый день, <?= Html::encode($user->username) ?>,</h3>

        <p>Вы запросили сброс пароля на сайте спортивных прогнозов. Для сброса пароля пройдите по ссылке:</p>

        <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>

        <p>Если Вы не запрашивали данное действие, свяжитесь с администрацией по адресу <?= Html::mailto(Yii::$app->params['adminEmail']);?></p>
    </div>
</div>
