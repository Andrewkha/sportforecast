<?php
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body style ="padding-top: 80px;">

<?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => 'Сайт спортивных прогнозов',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-inverse navbar-fixed-top',
                ],
            ]);
            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'encodeLabels' => false,
                'items' => [
                    ['label' => 'Новости', 'url' => ['/news/index']],
                    ['label' => 'О сайте', 'url' => ['/site/about']],
                    ['label' => 'Контакты', 'url' => ['/site/contact']],

                    Yii::$app->user->can('administrator')?
                        [
                            'label' => 'Администрирование',
                            'items' => [
                                ['label' => 'Команды', 'url' => ['/admin/teams']],
                                '<li class="divider"></li>',
                                ['label' => 'Страны', 'url' => ['/admin/countries']],
                                '<li class="divider"></li>',
                                ['label' => 'Турниры', 'url' => ['/admin/tournaments']],
                                '<li class="divider"></li>',
                                ['label' => 'Новости', 'url' => ['/admin/news']],
                                '<li class="divider"></li>',
                                ['label' => 'Пользователи', 'url' => ['/admin/users']],
                                '<li class="divider"></li>',
                                ['label' => 'Журнал', 'url' => ['/admin/log']],
                            ]
                        ]:

                        '',

                    Yii::$app->user->isGuest ?
                        ['label' => 'Вход', 'url' => ['/site/login']] :
                        [
                            'label' => Yii::$app->user->identity->username,
                            'items' => [
                                ['label' => 'Мои турниры', 'url' => ['/tournaments/index']],
                                '<li class="divider"></li>',
                                ['label' => 'Профиль', 'url' => ['/site/profile']],
                                '<li class="divider"></li>',
                                ['label' => 'Выход', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']]
                            ],
                        ],

                    Yii::$app->user->isGuest ?
                        ['label' => 'Регистрация', 'url' => ['/site/signup']] :
                        '',

                    !Yii::$app->user->isGuest ?
                        [
                            'label' => Html::img(Yii::$app->user->identity->fileUrl, ['height' => '40']), 'url' => ['/site/profile']
                        ]:
                        '',
                ],
            ]);
            NavBar::end();
        ?>

        <div class="container-fluid">
            <div class = "row">
                <?= Breadcrumbs::widget([
                    'options' => [
                        'class' => 'col-xs-12 col-xs-offset-0 col-sm-offset-1 col-sm-10 breadcrumb'
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>
            </div>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12 col-sm-10 col-sm-offset-1">
                    <p class="pull-left">&copy; Andrewkha web studio :) <?= date('Y') ?></p>
                    <p class="pull-right"><?= Yii::powered() ?></p>
                </div>
            </div>
        </div>
    </footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
