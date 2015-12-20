<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/30/2015
 * Time: 4:12 PM
 */

namespace app\models\traits;

use Yii;
use app\models\users\UsersTournaments;
use app\models\tournaments\Tournaments;
use app\models\users\Users;
use app\models\result\Result;
use app\models\news\News;
use app\models\forecasts\Forecasts;
use yii\db\Query;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use app\models\tournaments\TeamTournaments;

trait gameTrait
{

    //get array of games grouped by tour,
    public static function getGamesGroupedByTour($tournament, $tours) {

        //first we need to get the ids of teams

        $id_teams = ArrayHelper::getColumn(TeamTournaments::find()->where(['id_tournament' => $tournament])->all(), 'id');

        $games = self::find()
            ->where(['in', 'id_team_home', $id_teams])
            ->orWhere(['in', 'id_team_guest', $id_teams])
            ->andWhere(['in', 'tour', $tours])->with('idTeamHome.idTeam', 'idTeamGuest.idTeam')
            ->orderBy('tour', 'asc')->addOrderBy('date_time_game', 'asc')
            ->all();

        $return = [];

        foreach($tours as $tour) {
            foreach($games as $k => $game) {
                if($game->tour == $tour) {
                    $game->scenario = 'game_update';
                    $return[$tour][] = ArrayHelper::remove($games, $k);
                }
            }
        }

        $tourGames = [];
        foreach($return as $k => $game) {
            $tourGames[$k] = new ArrayDataProvider([
                'allModels' => $game,
                'key' => 'id_game'
            ]);
        }

        return $tourGames;
    }

    //get results for the tour in the tournament
    public static function getTourResults($tournament, $tour) {

        $id_teams = ArrayHelper::getColumn(TeamTournaments::find()->where(['id_tournament' => $tournament])->all(), 'id');

        $games = self::find()
            ->where(['or', ['in', 'id_team_home', $id_teams],['in', 'id_team_guest', $id_teams]])
            ->andWhere(['tour' => $tour])
            ->with('idTeamHome.idTeam', 'idTeamGuest.idTeam')
            ->orderBy('date_time_game', 'asc')
            ->all();

        return $games;
    }

    public static function getGamesGroupedByTourWithForecast($tournament, $tours, $user) {

        //first we need to get the ids of teams

        $id_teams = ArrayHelper::getColumn(TeamTournaments::find()->where(['id_tournament' => $tournament])->all(), 'id');

        $games = self::find()
            ->where(['in', 'id_team_home', $id_teams])
            ->orWhere(['in', 'id_team_guest', $id_teams])
            ->andWhere(['in', 'tour', $tours])
            ->with('idTeamHome.idTeam', 'idTeamGuest.idTeam')
            ->orderBy('tour', 'asc')->addOrderBy('date_time_game', 'asc')
            ->asArray()
            ->indexBy('id_game')
            ->all();

        $forecasts = Forecasts::find()
            ->where(['in', 'id_game', ArrayHelper::getColumn($games, 'id_game')])
            ->andWhere(['id_user' => $user])
            ->indexBy('id_game')
            ->asArray()
            ->all();

        $return = [];

        foreach($tours as $tour) {
            foreach($games as $k => $game) {
                if($game['tour'] == $tour) {
                    $return[$tour][$k] = ArrayHelper::remove($games, $k);
                    if(isset($forecasts[$k])) {

                        $return[$tour][$k]['fpoints'] = $forecasts[$k]['points'];
                        $return[$tour][$k]['id_user_forecast'] = $forecasts[$k]['id_user'];
                        $return[$tour][$k]['fscore_home'] = $forecasts[$k]['fscore_home'];
                        $return[$tour][$k]['fscore_guest'] = $forecasts[$k]['fscore_guest'];
                        $return[$tour][$k]['f_id'] = $forecasts[$k]['id'];
                        $return[$tour][$k]['f_date'] = $forecasts[$k]['date'];
                    }
                }
            }
        }

        $tourGames = [];
        foreach($return as $k => $game) {
            $tourGames[$k] = new ArrayDataProvider([
                'allModels' => $game,
                'key' => 'id_game'
            ]);
        }

        return $tourGames;
    }

    //get array of future games ~2 tours grouped by tour for the tournament

    private static function getFutureGamesGroupedByTour($tournament) {

        $trn = Tournaments::findOne($tournament);
        //first we need to get the ids of teams

        $id_teams = ArrayHelper::getColumn(TeamTournaments::find()->where(['id_tournament' => $tournament])->all(), 'id');

        $games = self::find()
            ->where(['in', 'id_team_home', $id_teams])
            ->orWhere(['in', 'id_team_guest', $id_teams])
            ->andWhere(['>=', 'date_time_game', time()])
            ->with('idTeamHome.idTeam', 'idTeamGuest.idTeam')
            ->orderBy(['date_time_game' =>  SORT_ASC])
            ->addOrderBy(['tour' => SORT_ASC])
            ->limit((int)ceil($trn->num_tours / 2) + 1)
            ->all();

        $tours = array_unique(ArrayHelper::getColumn($games, 'tour'));

        $return = [];
        foreach($tours as $tour) {
            foreach($games as $k => $game) {
                if($game->tour == $tour) {
                    $return[$tour][] = ArrayHelper::remove($games, $k);
                }
            }
        }

        $dataProvider = [];
        foreach($return as $k => $one) {
            $dataProvider[$k] = new ArrayDataProvider([
                'allModels' => $one,
            ]);
        };

        return $dataProvider;
    }

    //get future games ~2 tours for all tournaments

    public static function getAllFutureGames() {

        $tournaments = Tournaments::find()
            ->where(['or', ['is_active' => Tournaments::GOING], ['is_active' => Tournaments::NOT_STARTED]])
            ->all();

        $return = [];
        foreach($tournaments as $k => $tournament) {

            $return[$k]['tournament'] = $tournament->tournament_name;
            $return[$k]['id_tournament'] = $tournament->id_tournament;
            $return[$k]['games'] = self::getFutureGamesGroupedByTour($tournament->id_tournament);
        }

        return $return;
    }

    //get array of future games ~2 tours grouped by tour for the tournament with forecasts for the user
    private static function getFutureGamesGroupedByTourWithForecast($tournament, $user) {

        $trn = Tournaments::findOne($tournament);
        //first we need to get the ids of teams

        $id_teams = ArrayHelper::getColumn(TeamTournaments::find()->where(['id_tournament' => $tournament])->all(), 'id');

        $games = self::find()
            ->with('idTeamHome.idTeam', 'idTeamGuest.idTeam')
            ->where(['or', ['in', 'id_team_home', $id_teams], ['in', 'id_team_guest', $id_teams]])
            ->andWhere(['>=', 'date_time_game', time()])
            ->orderBy(['tour' =>  SORT_ASC])
            ->addOrderBy(['date_time_game' => SORT_ASC])
            ->limit((int)ceil($trn->num_tours / 2) + 1)
            ->indexBy('id_game')
            ->asArray()
            ->all();

        $forecasts = Forecasts::find()
            ->where(['in', 'id_game', ArrayHelper::getColumn($games, 'id_game')])
            ->andWhere(['id_user' => $user])
            ->indexBy('id_game')
            ->asArray()
            ->all();

        $tours = array_unique(ArrayHelper::getColumn($games, 'tour'));

        $return = [];
        foreach($tours as $tour) {
            foreach($games as $k => $game) {
                if($game['tour'] == $tour) {
                    $return[$tour][$k] = ArrayHelper::remove($games, $k);
                    if(isset($forecasts[$k])) {

                        $return[$tour][$k]['fpoints'] = $forecasts[$k]['points'];
                        $return[$tour][$k]['id_user_forecast'] = $forecasts[$k]['id_user'];
                        $return[$tour][$k]['fscore_home'] = $forecasts[$k]['fscore_home'];
                        $return[$tour][$k]['fscore_guest'] = $forecasts[$k]['fscore_guest'];
                        $return[$tour][$k]['f_id'] = $forecasts[$k]['id'];
                        $return[$tour][$k]['f_date'] = $forecasts[$k]['date'];
                    }
                    $return[$tour][$k]['recent'] = new ArrayDataProvider([
                        'allModels' => Result::getLastFiveConfrontations($game['idTeamHome']['id_team'], $game['idTeamGuest']['id_team'])
                    ]);
                    $return[$tour][$k]['recentHome'] = new ArrayDataProvider([
                        'allModels' => Result::getLastFiveGames($game['idTeamHome']['id_team'])
                    ]);
                    $return[$tour][$k]['recentGuest'] = new ArrayDataProvider([
                        'allModels' => Result::getLastFiveGames($game['idTeamGuest']['id_team'])
                    ]);
                }
            }
        }

        $dataProvider = [];
        foreach($return as $k => $one) {
            $dataProvider[$k] = new ArrayDataProvider([
                'allModels' => $one,
            ]);
        };

        return $dataProvider;
    }

    //getting future games for all active and pending tournaments user participates
    public static function getAllFutureGamesWithForecast($user) {

        $tournaments = Tournaments::find()
            ->joinWith('usersTournaments', false)
            ->where(['or', ['is_active' => Tournaments::GOING], ['is_active' => Tournaments::NOT_STARTED]])
            ->andWhere(['sf_users_tournaments.id_user' => $user])
            ->all();

        $return = [];
        foreach($tournaments as $k => $tournament) {

            $return[$k]['tournament'] = $tournament->tournament_name;
            $return[$k]['id_tournament'] = $tournament->id_tournament;
            $return[$k]['games'] = self::getFutureGamesGroupedByTourWithForecast($tournament->id_tournament, $user);
        }

        return $return;
    }

    //get array of recent games ~2 tours grouped by tour for the tournament

    private static function getRecentGamesGroupedByTour($tournament) {

        $trn = Tournaments::findOne($tournament);
        //first we need to get the ids of teams

        $id_teams = ArrayHelper::getColumn(TeamTournaments::find()->where(['id_tournament' => $tournament])->all(), 'id');

        $games = self::find()
            ->where(['in', 'id_team_home', $id_teams])
            ->orWhere(['in', 'id_team_guest', $id_teams])
            ->andWhere(['<', 'date_time_game', time()])
            ->with('idTeamHome.idTeam', 'idTeamGuest.idTeam')
            ->orderBy(['tour' => SORT_DESC])
            ->addOrderBy(['date_time_game' => SORT_ASC])
            ->limit((int)ceil($trn->num_tours / 2) + 1)
            ->all();

        $tours = array_unique(ArrayHelper::getColumn($games, 'tour'));

        $return = [];
        foreach($tours as $tour) {
            foreach($games as $k => $game) {
                if($game->tour == $tour) {
                    $return[$tour][] = ArrayHelper::remove($games, $k);
                }
            }
        }

        $dataProvider = [];
        foreach($return as $k => $one) {
            $dataProvider[$k] = new ArrayDataProvider([
                'allModels' => $one,
            ]);
        };

        return $dataProvider;
    }

    public static function getAllRecentGames() {

        $tournaments = Tournaments::find()
            ->where(['is_active' => Tournaments::GOING])
            ->all();

        $return = [];
        foreach($tournaments as $k => $tournament) {

            $return[$k]['tournament'] = $tournament->tournament_name;
            $return[$k]['id_tournament'] = $tournament->id_tournament;
            $return[$k]['games'] = self::getRecentGamesGroupedByTour($tournament->id_tournament);
        }

        return $return;
    }

    //get array of recent games ~2 tours grouped by tour for the tournament with forecasts for the user
    private static function getRecentGamesGroupedByTourWithForecast($tournament, $user) {

        $trn = Tournaments::findOne($tournament);
        //first we need to get the ids of teams

        $id_teams = ArrayHelper::getColumn(TeamTournaments::find()->where(['id_tournament' => $tournament])->all(), 'id');

        $games = self::find()
            ->with('idTeamHome.idTeam', 'idTeamGuest.idTeam')
            ->where(['or', ['in', 'id_team_home', $id_teams], ['in', 'id_team_guest', $id_teams]])
            ->andWhere(['<', 'date_time_game', time()])
            ->orderBy(['tour' =>  SORT_DESC])
            ->addOrderBy(['date_time_game' => SORT_ASC])
            ->limit((int)ceil($trn->num_tours / 2) + 1)
            ->indexBy('id_game')
            ->asArray()
            ->all();

        $forecasts = Forecasts::find()
            ->where(['in', 'id_game', ArrayHelper::getColumn($games, 'id_game')])
            ->andWhere(['id_user' => $user])
            ->indexBy('id_game')
            ->asArray()
            ->all();

        $tours = array_unique(ArrayHelper::getColumn($games, 'tour'));

        $return = [];
        foreach($tours as $tour) {
            foreach($games as $k => $game) {
                if($game['tour'] == $tour) {
                    $return[$tour][$k] = ArrayHelper::remove($games, $k);
                    if(isset($forecasts[$k])) {
                        $return[$tour][$k]['fpoints'] = $forecasts[$k]['points'];
                        $return[$tour][$k]['id_user_forecast'] = $forecasts[$k]['id_user'];
                        $return[$tour][$k]['fscore_home'] = $forecasts[$k]['fscore_home'];
                        $return[$tour][$k]['fscore_guest'] = $forecasts[$k]['fscore_guest'];
                        $return[$tour][$k]['f_id'] = $forecasts[$k]['id'];
                        $return[$tour][$k]['f_date'] = $forecasts[$k]['date'];
                    }
                }
            }
        }

        $dataProvider = [];
        foreach($return as $k => $one) {
            $dataProvider[$k] = new ArrayDataProvider([
                'allModels' => $one,
            ]);
        };

        return $dataProvider;
    }

    //getting recent games for all active and pending tournaments user participates with forecast
    public static function getAllRecentGamesWithForecast($user) {

        $tournaments = Tournaments::find()
            ->joinWith('usersTournaments', false)
            ->where(['or', ['is_active' => Tournaments::GOING], ['is_active' => Tournaments::NOT_STARTED]])
            ->andWhere(['sf_users_tournaments.id_user' => $user])
            ->all();

        $return = [];
        foreach($tournaments as $k => $tournament) {

            $return[$k]['tournament'] = $tournament->tournament_name;
            $return[$k]['id_tournament'] = $tournament->id_tournament;
            $return[$k]['games'] = self::getRecentGamesGroupedByTourWithForecast($tournament->id_tournament, $user);
        }

        return $return;
    }

    public static function sendTourResults($tour, $tournament) {

        $trn = Tournaments::findOne($tournament);

        //if not last tour - just send notifications
        if($tour != $trn->num_tours) {

            $recipients =  ArrayHelper::getColumn(UsersTournaments::find()
                ->joinWith('idUser')
                ->where(['and', ['id_tournament' => $tournament], ['notification' => UsersTournaments::NOTIFICATION_ENABLED], ['sf_users.active' => Users::STATUS_ACTIVE]])
                ->all(),
                'idUser');

            $subject = "Результаты $tour тура - ".$trn->tournament_name;

            $thisTourForecastStanding = new ArrayDataProvider([
                'allModels' => Forecasts::getTopFiveForecastersWithPoints($tournament, $tour)
            ]);

            //creating news for the tour

            $news = new News();
            $news->subject = "Результаты $tour тура";
            $news->id_tournament = $tournament;
            $news->body = self::generateTourNews($tournament, $tour);

            $news->save();

            foreach($recipients as $one) {
                $content = new ArrayDataProvider([
                    'allModels' => Forecasts::getForecastResultUserTourTournament($one->id, $tour, $tournament),

                ]);

                $leaderAndUser = new ArrayDataProvider([
                    'allModels' => Forecasts::getLeaderAndUserPosition($one->id, $tournament),
                ]);

                $messages[] = Yii::$app->mailer->compose('forecastResult', [
                    'content' => $content,
                    'standings' => $leaderAndUser,
                    'user' => $one,
                    'tournament' => $trn->tournament_name,
                    'tour' => $tour,
                    'tourForecasts' => $thisTourForecastStanding
                ])
                    ->setFrom([Yii::$app->params['adminEmail'] => 'Sportforecast'])
                    ->setTo($one->email)
                    ->setSubject($subject);
            }

            if(!empty($messages)) {

                Yii::$app->mailer->sendMultiple($messages);
            }
        } else {

            $news = new News();
            $news->scenario = 'send';
            $news->subject = 'Закончен турнир '.$trn->tournament_name;
            $news->body = Tournaments::generateFinalNews($tournament);
            $news->id_tournament = $tournament;

            $news->save();
        }

    }

    private static function generateTourNews($tournament, $tour) {

        $trn = Tournaments::findOne($tournament);

        $games = new ArrayDataProvider([
            'allModels' => self::getTourResults($tournament, $tour),
            'pagination' => false,
            'sort' => false,
        ]);

        $forecasters = new ArrayDataProvider([
            'allModels' => Forecasts::getTopFiveForecastersWithPoints($tournament, $tour)
        ]);

        return Yii::$app->controller->renderPartial('@app/mail/_tourResultNews', ['trn' => $trn, 'games' => $games, 'forecasters' => $forecasters, 'tour' => $tour]);

    }

    //checking if the tour finished
    public static function isTourFinished($tournament, $tour) {

        $gamesIdForTourTournament = ArrayHelper::getColumn(Result::find()
            ->select(['id_game'])
            ->where(['and', ['tour' => $tour], ['id_tournament' => $tournament]])
            ->all(),
            'id_game');

        if(empty($gamesIdForTourTournament))
            return false;

        return !self::find()
            ->where(['in', 'id_game', $gamesIdForTourTournament])
            ->andWhere(['or', ['score_home' => null], ['score_guest' => null]])
            ->exists();
    }

    public static function getNumberOfGamesPerTour($tournament) {

        $games = ArrayHelper::getColumn(Result::find()->select('id_game')->where(['id_tournament' => $tournament])->all(), 'id_game');

        $tours = (new Query())
            ->select('tour')
            ->from('sf_games')
            ->where(['in', 'id_game', $games])
            ->all();

        if($tours) {

            $tours = ArrayHelper::getColumn($tours, 'tour');
            $tours = array_count_values($tours);
            ksort($tours);
        }
        return $tours;
    }
}