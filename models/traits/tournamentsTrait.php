<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/30/2015
 * Time: 4:24 PM
 */

namespace app\models\traits;

use app\models\tournaments\Tournaments;
use Yii;
use app\models\result\Result;
use app\models\games\Games;
use app\models\forecasts\Forecasts;
use yii\data\ArrayDataProvider;
use app\models\users\UsersTournaments;
use yii\db\ActiveQuery;
use yii\db\Query;
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

            $tours =  Games::getNumberOfGamesPerTour($tournament);
            do {

                if(isset($tours[$tour])) {
                    $gamesInTour = $tours[$tour];

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

    //new getForecastersList
    public function getForecastersListNew()
    {
        $forecasters = UsersTournaments::find()
            ->where(['id_tournament' => $this->id_tournament])
            ->with('idUser')
            ->asArray()
            ->all();

        $tours =  Games::getNumberOfGamesPerTour($this->id_tournament);
        foreach($forecasters as &$one)
        {
            $one['tours'] = Forecasts::getUserForecastTour($one['id_user'], $this->id_tournament, $tours);
        }

        return $forecasters;
    }

    //get list of not finished tournaments with leader info
    public static function activePendingTournamentsWithLeader()
    {
        $tournaments = self::find()
            ->where(['or', ['is_active' => self::NOT_STARTED], ['is_active' => self::GOING]])
            ->column();

        if(!empty($tournaments))
        {
            return self::unionQueryPrep($tournaments);
        } else
            return [];

    }

    //get list of all tournaments user participates in

    public static function finishedTournamentsUserParticipated($user)
    {
        $participates = UsersTournaments::find()->userParticipates($user)->all();

        $tournaments = self::find()
            ->where(['is_active' => self::FINISHED])
            ->andWhere(['in', 'id_tournament', ArrayHelper::getColumn($participates, 'id_tournament')])
            ->column();

        if(!empty($tournaments))
        {
            return self::unionQueryPrep($tournaments);
        } else
            return UsersTournaments::find()->findModel(NULL, NULL);
    }
    //list of finished tournaments user participated in

    public static function allTournamentsUserParticipated($user)
    {
        $participates = UsersTournaments::find()->userParticipates($user)->all();

        $tournaments = self::find()
            ->andWhere(['in', 'id_tournament', ArrayHelper::getColumn($participates, 'id_tournament')])
            ->column();

        if(!empty($tournaments))
        {
            return self::unionQueryPrep($tournaments);
        } else
            return UsersTournaments::find()->findModel(NULL, NULL)->all();
    }

    //get list of active and pending tournaments where user doesn't participate with leader info
    public static function getActivePendingTournamentsNotParticipate($user)
    {
        $participates = UsersTournaments::find()->userParticipates($user)->all();

        $tournaments = self::find()
            ->where(['or', ['is_active' => self::NOT_STARTED], ['is_active' => self::GOING]])
            ->andWhere(['not in', 'id_tournament', ArrayHelper::getColumn($participates, 'id_tournament')])
            ->column();

        if(!empty($tournaments))
        {
            return self::unionQueryPrep($tournaments);
        } else
            return UsersTournaments::find()->findModel(NULL, NULL)->all();
    }

    //list of unfinished tournaments ures participates in with leader info
    public static function getActivePendingTournamentsUserParticipate($user) {

        $participates = UsersTournaments::find()->userParticipates($user)->all();

        $tournaments = self::find()
            ->where(['or', ['is_active' => self::NOT_STARTED], ['is_active' => self::GOING]])
            ->andWhere(['in', 'id_tournament', ArrayHelper::getColumn($participates, 'id_tournament')])
            ->column();

        if(!empty($tournaments))
        {
            return self::unionQueryPrep($tournaments);
        } else
            return UsersTournaments::find()->findModel(NULL, NULL)->all();

    }

    //get list of all where user doesn't participate with leader info
    public static function getAllTournamentsUserNotParticipate($user) {

        $participates = UsersTournaments::find()->userParticipates($user)->all();

        $tournaments = self::find()
            ->andWhere(['not', ['id_tournament' => ArrayHelper::getColumn($participates, 'id_tournament')]])
            ->column();

        if(!empty($tournaments))
        {
            return self::unionQueryPrep($tournaments);
        } else
            return UsersTournaments::find()->findModel(NULL, NULL)->all();

    }

    private static function unionQueryPrep($array)
    {
        $query = [];
        foreach ($array as $one)
            $query[] = UsersTournaments::find()
                ->where(['{{%users_tournaments}}.id_tournament' => $one])
                ->joinWith('idTournament.country0')
                ->joinWith('idUser')
                ->orderBy(['points' => SORT_DESC])
                ->limit(1);

        $count = count($query);

        $toExecute = $query[0];
        for($i = 0; $i < $count - 1; $i++)
            $toExecute = $toExecute->union($query[$i + 1]);

        return $toExecute->all();
    }

    public static function generateFinalNews($tournament) {

        $trn = self::findOne($tournament);

        $forecasters = new ArrayDataProvider([
            'allModels' => UsersTournaments::topThreeForecastersForTournament($tournament)
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