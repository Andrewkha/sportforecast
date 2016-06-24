<?php
use kartik\widgets\Growl;
use yii\helpers\Html;
use app\components\widgets\News;
use app\components\grid\extendedGridView;
use app\components\widgets\frontPageGames;
/** @var $this yii\web\View
   * @var $futureGames
   * @var $recentGames
   * @var $tournaments
 *
 */
$this->title = 'Сайт спортивных прогнозов';
?>

<?php
if(Yii::$app->session->hasFlash('success')) {

    echo Growl::widget([
        'type' => Growl::TYPE_SUCCESS,
        'icon' => 'glyphicon glyphicon-ok-sign',
        'body' => Yii::$app->session->getFlash('success'),
        'showSeparator' => true,
    ]);
}
?>

<div class = "row">
    <div class="col-xs-10 col-xs-offset-1 text-center">
        <h2>Добро пожаловать на сайт спортивных прогнозов!</h2>
        <p class="lead"><?= Html::a('Войдите', ['site/login']);?>/<?= Html::a('зарегистрируйтесь', ['site/signup']);?> чтобы принять участие</p>
    </div>
</div>

<div class="body-content">

    <div class = "row">
        <div class = "col-xs-12 col-xs-offset-0 col-md-6 col-md-offset-1">
            <?= frontPageGames::widget(['type' => 'future']); ?>
            <?= frontPageGames::widget(['type' => 'recent']); ?>
        </div>

        <div class = "col-xs-8 col-xs-offset-0 col-md-4 col-lg-3 col-lg-offset-1" id = 'right'>

            <div class = "text-center">
                <div class ="row">
                    <?= extendedGridView::widget([
                        'dataProvider' => $tournaments,
                        'emptyText' => 'Нет текущих турниров',
                        'caption' => 'Текущие турниры',
                        'condensed' => true,
                        'captionOptions' => [
                            'bordered' => false,
                            'class' => 'text-center',
                            'style' => 'font-size: 1.5em;'
                        ],
                        'bordered' => false,
                        'summary' => '',
                        'columns' => [
                            [
                                //'attribute' => 'idTournament.tournament_name',
                                'header' => 'Турнир',
                                'contentOptions' => [
                                    'class' => 'text-left',
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-7'
                                ],
                                'content' => function($model) {
                                    return Html::a($model->tournament_name, ['tournaments/details', 'id' => $model->id_tournament]);
                                },
                                'format' => 'url',
                            ],

                            [
                                'header' => 'Лидер прогноза',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-4'
                                ],
                                'content' => function($model) {
                                    return (isset($model->usersTournaments[0]->idUser->username))? $model->usersTournaments[0]->idUser->username :'-';
                                },
                            ],

                            [
                                'header' => 'Очки лидера',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-1'
                                ],
                                'content' => function($model) {
                                    return (isset($model->usersTournaments[0]->points))? $model->usersTournaments[0]->points :'-';
                                },
                            ],
                        ]
                    ])

                    ?>
                    <p class = 'text-right'>
                        <?= Html::a("<i class = 'fa fa-futbol-o'></i> Все турниры", ['tournaments/index']);?>
                    </p>
                </div>

            </div>

            <div class = "text-center">
                <div class ="row">
                    <?= extendedGridView::widget([
                        'dataProvider' => $finishedTournaments,
                        'showOnEmpty' => false,
                        'emptyText' => '',
                        'caption' => 'Законченные турниры',
                        'captionOptions' => [
                            'bordered' => false,
                            'class' => 'text-center',
                            'style' => 'font-size: 1.5em;'
                        ],
                        'condensed' => true,
                        'bordered' => false,
                        'summary' => '',
                        'columns' => [

                            [
                                'header' => 'Турнир',
                                'contentOptions' => [
                                    'class' => 'text-left',
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-6'
                                ],
                                'content' => function($model) {
                                    return Html::a($model->tournament_name, ['tournaments/details', 'id' => $model->id_tournament]);
                                },
                                'format' => 'url',
                            ],

                            [
                                'header' => 'Победитель',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-4'
                                ],
                                'content' => function($model) {
                                    return (isset($model->usersTournaments[0]->idUser->username))? $model->usersTournaments[0]->idUser->username :'-';
                                },
                            ],

                            [
                                'header' => 'Очки победителя',
                                'contentOptions' => [
                                    'style' => 'vertical-align:middle',
                                ],
                                'headerOptions' => [
                                    'style' => 'text-align:center',
                                ],
                                'options' => [
                                    'class' => 'col-xs-1'
                                ],
                                'content' => function($model) {
                                    return (isset($model->usersTournaments[0]->points))? $model->usersTournaments[0]->points :'-';
                                },
                            ],
                        ]
                    ])

                    ?>
                    <p class = 'text-right'>
                        <?= Html::a("<i class = 'fa fa-futbol-o'></i> Все турниры", ['tournaments/index']);?>
                    </p>
                </div>

            </div>

            <div class="text-center">
                <div class = "row">
                    <?= News::widget(['title' => 'Новости']);?>
                </div>
            </div>
        </div>
    </div>
</div>

