<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/30/2015
 * Time: 4:24 PM
 */

namespace app\models\traits;

use Yii;
use app\models\result\Result;
use app\models\games\Games;
use app\models\forecasts\Forecasts;
use yii\data\ArrayDataProvider;
use app\models\users\UsersTournaments;
use yii\helpers\ArrayHelper;

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

        $participates = UsersTournaments::getTournamentsUserParticipates($user);

        $tournaments = self::find()
            ->with(['country0'])
            ->where(['not', ['id_tournament' => ArrayHelper::getColumn($participates, 'id_tournament')]])
            ->asArray()
            ->all();

        return self::leaderAndPointsAssignment($tournaments);
    }

    //get list of active and pending tournaments where user doesn't participate with leader info
    public static function getActivePendingTournamentsNotParticipate($user) {

        $participates = UsersTournaments::getTournamentsUserParticipates($user);

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

        $trn = self::findOne($tournament);


        $forecasters = new ArrayDataProvider([
            'allModels' => Forecasts::getTopThreeForecastersWithPoints($tournament)
        ]);

        $standings = new ArrayDataProvider([
            'allModels' => Result::getStandings($tournament)
        ]);

        return Yii::$app->controller->renderPartial('@app/mail/_tournamentFinishedNews', ['trn' => $trn, 'forecasters' => $forecasters, 'standings' => $standings]);
    }

    public static function getAutoprocessTournaments() {

        return self::find()
            ->where(['not', ['is_active' => self::FINISHED]])
            ->andWhere(['enableAutoprocess' => 1])
            ->andWhere(['not', ['autoProcessURL' => null]])
            ->all();
    }
}