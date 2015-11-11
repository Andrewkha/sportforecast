<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/30/2015
 * Time: 4:24 PM
 */

namespace app\models\traits;

use app\models\result\Result;
use app\components\grid\extendedGridView;
use app\models\games\Games;
use app\models\forecasts\Forecasts;
use yii\data\ArrayDataProvider;
use app\models\users\UsersTournaments;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

trait tournamentsTrait
{

    //get next tour number
    public static function getNextTour($tournament) {

        $trn = self::findOne($tournament);

        $tour = Result::find()
            ->select(['min(tour) as tour'])
            ->where(['>', 'dtime', time()])
            ->andWhere(['id_tournament' => $tournament])
            ->all();

        if($tour[0]->tour != NULL) {

            $tour = $tour[0]->tour;
            if($trn->num_tours == $tour) {
                return $trn->num_tours;
            }

            do {

                if(isset(Games::getNumberOfGamesPerTour($tournament)[$tour])) {
                    $gamesInTour = Games::getNumberOfGamesPerTour($tournament)[$tour];

                    $remainingGamesInTour = Result::find()
                        ->select(['id_game'])
                        ->where(['>', 'dtime', time()])
                        ->andWhere(['id_tournament' => $tournament, 'tour' => $tour])
                        ->count();

                    $tour++;
                } else {
                    return $tour-1;
                }
            } while($gamesInTour != $remainingGamesInTour);

            return $tour-1;
        } else {

            return NULL;
        }

    }

    //getting the list of the forecasters for the tournament. Both with forecasts and with no

    public function getForecastersList() {

        $forecasters1 = Forecasts::getForecastersWithPoints($this->id_tournament);

        $forecastersList1 = [];
        $forecastersList2 = [];

        //getting tournament forecasters with forecasts
        foreach($forecasters1 as $k => $one) {

            $forecastersList1[$k]['id_user'] = $one->id_user;
            $forecastersList1[$k]['points'] = $one->points;
            $forecastersList1[$k]['name'] = $one->idUser->username;
            $forecastersList1[$k]['avatar'] = $one->idUser->avatar;
            $forecastersList1[$k]['tours'] = Forecasts::getUserForecastTour($one->id_user, $this->id_tournament);

        }

        //getting forecasters with no forecasts
        $forecasters2 = UsersTournaments::find()
            ->joinWith('idUser')
            ->where(['and', "id_tournament = $this->id_tournament", ['not in', 'id_user', ArrayHelper::getColumn($forecasters1, 'id_user')] ])
            ->all();

        foreach($forecasters2 as $k => $one) {

            $forecastersList2[$k]['id_user'] = $one->id_user;
            $forecastersList2[$k]['points'] = '-';
            $forecastersList2[$k]['name'] = $one->idUser->username;
            $forecastersList2[$k]['avatar'] = $one->idUser->avatar;
            $forecastersList2[$k]['tours'] = Forecasts::getUserForecastTour($one->id_user, $this->id_tournament);
        }

        return ArrayHelper::merge($forecastersList1, $forecastersList2);
    }

    //get list of all tournaments where user doesn't participate with leader info
    public static function getAllTournamentsNotParticipate($user) {

        $participates = UsersTournaments::getAllUserTournamentsAndPosition($user);

        $tournaments = self::find()
            ->with(['country0'])
            ->where(['not', ['id_tournament' => ArrayHelper::getColumn($participates, 'id_tournament')]])
            ->asArray()
            ->all();

        return self::leaderAndPointsAssignment($tournaments);
    }

    //get list of active and pending tournaments where user doesn't participate with leader info
    public static function getActivePendingTournamentsNotParticipate($user) {

        $participates = UsersTournaments::getAllUserTournamentsAndPosition($user);

        $tournaments = self::find()
            ->with(['country0'])
            ->where(['not', ['id_tournament' => ArrayHelper::getColumn($participates, 'id_tournament')]])
            ->andWhere(['or', ['is_active' => self::NOT_STARTED], ['is_active' => self::GOING]])
            ->asArray()
            ->all();

        return self::leaderAndPointsAssignment($tournaments);
    }

    private static function leaderAndPointsAssignment($tournaments) {

        foreach($tournaments as $k => &$tournament) {
            $forecasters = Forecasts::getForecastersWithPoints($tournament['id_tournament']);
            if(empty($forecasters)) {
                $tournament['leader'] = '-';
                $tournament['leaderPoints'] = '-';
            } else {
                $tournament['leader'] = $forecasters[0]['idUser']['username'];
                $tournament['leaderPoints'] = $forecasters[0]['points'];
            }
        }

        return $tournaments;
    }

    public static function generateFinalNews($tournament) {

        $content = '';

        $trn = self::findOne($tournament);
        $content.= Html::tag('p', "Закончился турнир $trn->tournament_name. Пожалуйста, ознакомьтесь с его результатами");

        $content .= "Подробную информацию о турнире можно посомотреть на его ".Html::a('странице', ['@web/tournaments/details', 'id' => $trn->id_tournament]);
        $content .= "<br>";

        $content .= "<div class = 'row'>";

        $forecasters = new ArrayDataProvider([
            'allModels' => Forecasts::getTopThreeForecastersWithPoints($tournament)
        ]);

        $content .= extendedGridView::widget([
            'dataProvider' => $forecasters,
            'caption' => 'Победители прогноза',
            'summary' => false,
            'options' => [
                'class' => 'col-xs-12 col-md-5 col-lg-5',
            ],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'contentOptions' => [
                        'align' => 'center',
                        'style' => 'vertical-align:middle',
                    ],
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'options' => [
                        'class' => 'col-xs-1',
                    ],
                    'header' => 'Место',
                ],

                [
                    'header' => 'Пользователь',
                    'vAlign' => 'middle',
                    'options' => [
                        'class' => 'col-xs-9',
                    ],
                    'hAlign' => 'left',
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'content' => function($model) {

                        return $model->idUser->username;
                    }
                ],

                [
                    'header' => 'Очки',
                    'attribute' => "points",
                    'vAlign' => 'middle',
                    'options' => [
                        'class' => 'col-xs-1',
                    ],
                    'hAlign' => 'center',
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                ],
            ]
        ]);
        $standings = new ArrayDataProvider([
            'allModels' => Result::getStandings($tournament)
        ]);

        $content .= extendedGridView::widget([
            'dataProvider' => $standings,
            'options' => [
                'class' => 'col-xs-12 col-md-5 col-md-offset-1 col-lg-6 col-lg-offset-1'
            ],
            'summary' => false,
            'caption' => 'Турнирная таблица',
            'columns' =>  [
                [
                    'header' => 'Место',
                    'class' => 'yii\grid\SerialColumn',
                    'contentOptions' => [
                        'align' => 'center',
                        'style' => 'vertical-align:middle',
                    ],
                    'options' => [
                        'class' => 'col-xs-1'
                    ],
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                ],

                [
                    'header' => 'Команда',
                    'content' => function($model) {
                        return $model['team_name'];
                    },
                    'contentOptions' => [
                        'style' => 'vertical-align:middle',
                    ],
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'options' => [
                        'class' => 'col-xs-9'
                    ],
                ],

                [
                    'header' => 'Игры',
                    'attribute' => 'games_played',
                    'contentOptions' => [
                        'align' => 'center',
                        'style' => 'vertical-align:middle',
                    ],
                    'options' => [
                        'class' => 'col-xs-1'
                    ],
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'content' => function ($model) {
                        return (isset($model['games_played']))? $model['games_played'] : 0;
                    }
                ],

                [
                    'header' => 'Очки',
                    'contentOptions' => [
                        'align' => 'center',
                        'style' => 'vertical-align:middle',
                    ],
                    'options' => [
                        'class' => 'col-xs-1'
                    ],
                    'headerOptions' => [
                        'style' => 'text-align:center',
                    ],
                    'content' => function ($model) {
                        return (isset($model['pts']))? $model['pts'] : 0;
                    }
                ],
            ]
        ]);

        $content .= "</div>";

        return $content;
    }

    public static function getAutoprocessTournaments() {

        return self::find()
            ->where(['not', ['is_active' => self::FINISHED]])
            ->andWhere(['enableAutoprocess' => 1])
            ->andWhere(['not', ['autoProcessURL' => null]])
            ->all();
    }
}