<?php

namespace app\models\forecasts;

use app\models\users\UsersTournaments;
use Yii;
use app\models\users\Users;
use app\models\result\Result;
use app\models\games\Games;
use app\models\tournaments\Tournaments;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\db\Query;

/**
 *  * This is the model class for table "{{%forecasts}}".
 *
 * @property string $id
 * @property integer $id_game
 * @property string $id_user
 * @property integer $fscore_home
 * @property integer $fscore_guest
 * @property string $date
 * @property integer $points
 *
 * @property Games $idGame
 * @property Users $idUser
 */
class Forecasts extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%forecasts}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_game', 'id_user'], 'required'],
            [['id_game', 'id_user', 'fscore_home', 'fscore_guest', 'date', 'points'], 'integer'],
            [['fscore_home', 'fscore_guest'], 'validForecast']
        ];
    }

    public function behaviors() {

        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'date',
                'updatedAtAttribute' => 'date',
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_game' => 'Id Game',
            'id_user' => 'Id User',
            'fscore_home' => 'Fscore Home',
            'fscore_guest' => 'Fscore Guest',
            'date' => 'Date',
            'points' => 'Points',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdGame()
    {
        return $this->hasOne(Games::className(), ['id_game' => 'id_game']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'id_user']);
    }


    //assign forecast points after gave finishes

    /**
     * @param $game
     * @param $scoreHome
     * @param $scoreGuest
     * @var $forecasts Forecasts
     * @var $forecast Forecasts
     */
    public static function setForecastPoints($game, $scoreHome, $scoreGuest, $tournament) {

        $forecasts = self::find()->where(['id_game' => $game])->all();
        foreach($forecasts as $forecast) {
            $oldPoints = $forecast->points;

            $forecast->forecastPoints($scoreHome, $scoreGuest);
            $forecast->save(false);

            $userTournament = UsersTournaments::findOne(['id_tournament' => $tournament, 'id_user' => $forecast->id_user]);
            $userTournament->points = $userTournament->points - $oldPoints + $forecast->points;
            $userTournament->save(false);
        }
    }

    /**
     * @param $scoreHome
     * @param $scoreGuest
     * @return int
     */
    private function forecastPoints($scoreHome, $scoreGuest) {

        if(($this->fscore_home == $scoreHome) and ($this->fscore_guest == $scoreGuest)) return $this->points = 3;
        if(($this->fscore_home - $this->fscore_guest) == ($scoreHome - $scoreGuest)) return $this->points = 2;
        if((($this->fscore_home - $this->fscore_guest) > 0 and ($scoreHome - $scoreGuest) > 0) OR ((($this->fscore_home - $this->fscore_guest) < 0 and ($scoreHome - $scoreGuest) < 0))) return $this->points = 1;
        return $this->points = 0;
    }


    //get forecasters with points for the tournament
    public static function getForecastersWithPoints($tournament) {

        $games = ArrayHelper::getColumn(Result::find()->select('id_game')->where(['id_tournament' => $tournament])->all(), 'id_game');

        $forecasts = self::find()
            ->select(['sum(points) as points', 'id_user'])
            ->joinWith('idUser')
            ->where(['in', 'id_game', $games])
            ->groupBy('id_user')
            ->orderBy(['points' => SORT_DESC])
            ->all();

        return $forecasts;
    }

    //get top 5 forecasters with points for the tour in tournament
    public static function getTopFiveForecastersWithPoints($tournament, $tour) {

        $games = ArrayHelper::getColumn(Result::find()
            ->select('id_game')
            ->where(['id_tournament' => $tournament])
            ->andWhere(['tour' => $tour])
            ->all(),
        'id_game');

        $forecasts = self::find()
            ->select(['sum(points) as points', 'id_user'])
            ->joinWith('idUser')
            ->where(['in', 'id_game', $games])
            ->groupBy('id_user')
            ->limit(5)
            ->orderBy(['points' => SORT_DESC])
            ->all();

        return $forecasts;
    }

    //get forecasters with points for the tournament
    public static function getTopThreeForecastersWithPoints($tournament) {

        $games = ArrayHelper::getColumn(Result::find()->select('id_game')->where(['id_tournament' => $tournament])->all(), 'id_game');

        $forecasts = self::find()
            ->select(['sum(points) as points', 'id_user'])
            ->joinWith('idUser')
            ->where(['in', 'id_game', $games])
            ->groupBy('id_user')
            ->orderBy(['points' => SORT_DESC])
            ->limit(3)
            ->all();

        return $forecasts;
    }

    //getting the leader, leader points, user and user points for the tournament
    //todo search where this is used + rewrite
    public static function getLeaderAndUserPosition($user, $tournament) {

        $forecasters = self::getForecastersWithPoints($tournament);
        if(empty($forecasters)) {
            $result[0]['leader'] = '-';
            $result[0]['leaderPoints'] = '-';
            $result[0]['userPoints'] = '-';
            $result[0]['userPosition'] = '-';
        } else {
            $result[0]['leader'] = $forecasters[0]['idUser']['username'];
            $result[0]['leaderPoints'] = $forecasters[0]['points'];
            foreach($forecasters as $k => $one) {
                if($one['id_user'] == $user) {
                    $result[0]['userPoints'] = $one['points'];
                    $result[0]['userPosition'] = $k + 1;
                    break;
                } else {
                    $result[0]['userPoints'] = '-';
                    $result[0]['userPosition'] = '-';
                }
            }
        }
        $result[0]['user'] = $user;
        $result[0]['count'] = UsersTournaments::find()->where(['id_tournament' => $tournament])->count();

        return $result;
    }

    //get list of tours that forecaster did full forecasts (used for reminders) 0 - no forecast, 1 - partilal forecast, 2 - full forecast
    public static function getUserForecastTour($user, $tournament) {

        //list of games' tours there're forecasts for

        $toursForecasted = (new Query())
            ->select(['sf_result.tour'])
            ->from('sf_forecasts')
            ->join('left JOIN', 'sf_result', 'sf_forecasts.id_game = sf_result.id_game')
            ->where(['and', "sf_result.id_tournament = $tournament", "sf_forecasts.id_user = $user", ['not',['sf_forecasts.fscore_home' => null]]])
            ->all();

        $toursReal = Games::getNumberOfGamesPerTour($tournament);
        $fullTourForecast = [];

        if($toursForecasted) {

            $toursForecasted = ArrayHelper::getColumn($toursForecasted, 'tour');
            $toursForecasted = array_count_values($toursForecasted);
            ksort($toursForecasted);

            foreach($toursReal as $k => $one) {

                if(!isset($toursForecasted[$k]))
                    $fullTourForecast[$k] = 0;
                elseif($toursForecasted[$k] < $one)
                    $fullTourForecast[$k] = 1;
                else
                    $fullTourForecast[$k] = 2;
            }

            return $fullTourForecast;
        }

        foreach($toursReal as $k => $one) {

            $fullTourForecast[$k] = 0;
        }

        return $fullTourForecast;
    }

    //getting the list of games per tour for user with forecast status
    /**
     * @param $tour
     * @param $tournament
     * @param $user
     * @return array
     */
    public static function getTourUserForecastStatus($tour, $tournament, $user) {

        //getting the games for the tournament and tour
        $games = Result::find()
            ->where(['id_tournament' => $tournament, 'tour' => $tour])
            ->orderBy(['dtime' => SORT_ASC])
            ->all();

        $forecast = self::find()
            ->where(['in', 'id_game', ArrayHelper::getColumn($games, 'id_game')])
            ->andWhere(['id_user' => $user])
            ->all();

        $forecast = ArrayHelper::index($forecast, 'id_game');
        $match = [];

        foreach($games as $k => $game) {

            $match[$k]['id_game'] = $game->id_game;
            $match[$k]['time'] = date('d.m.y H:i',$game->dtime);
            $match[$k]['home_team'] = $game->home_team;
            $match[$k]['guest_team'] = $game->guest_team;
            if(array_key_exists($game->id_game, $forecast)) {
                $match[$k]['status'] = 1;
                $match[$k]['home_score_forecast'] = $forecast[$game->id_game]->fscore_home;
                $match[$k]['guest_score_forecast'] = $forecast[$game->id_game]->fscore_guest;
            } else {
                $match[$k]['status'] = 0;
                $match[$k]['home_score_forecast'] = '-';
                $match[$k]['guest_score_forecast'] = '-';
            }
        }

        return $match;
    }

    //getting the forecast summary for user and tournament grouped by tour
    public static function getUserForecastStatus($tournament, $user) {

        //getting the games for the tournament
        $games = Result::find()
            ->where(['id_tournament' => $tournament])
            ->all();

        $forecast = (new Query())
            ->select(['sum(points) as points', 'tour'])
            ->from('sf_forecasts')
            ->rightJoin('sf_games', 'sf_forecasts.id_game = sf_games.id_game')
            ->where(['and', "id_user = $user", ['in', 'sf_forecasts.id_game', ArrayHelper::getColumn($games, 'id_game')]])
            ->groupBy(['tour'])
            ->orderBy(['tour' => SORT_ASC])
            ->all();

        return $forecast;
    }

    //get forecast result for user, tour, tournament
    public static function getForecastResultUserTourTournament($user, $tour, $tournament) {

        $games = Result::getGamesTourTournament($tour, $tournament);

        $forecasts = self::find()
            ->where(['and', "id_user = $user", ['in', 'sf_forecasts.id_game', ArrayHelper::getColumn($games, 'id_game')]])
            ->asArray()
            ->all();

        $forecasts = ArrayHelper::index($forecasts, 'id_game');

        $games = ArrayHelper::toArray($games);

        foreach($games as &$one) {
            if(array_key_exists($one['id_game'], $forecasts)) {

                $one['fscore_home'] = $forecasts[$one['id_game']]['fscore_home'];
                $one['fscore_guest'] = $forecasts[$one['id_game']]['fscore_guest'];
                $one['fpoints'] = $forecasts[$one['id_game']]['points'];
                $one['status'] = true;
            } else {

                $one['fscore_home'] = '-';
                $one['fscore_guest'] = '-';
                $one['fpoints'] = '-';
                $one['status'] = false;
            }
        }
        ArrayHelper::multisort($games, 'dtime');
        return $games;
    }

    //todo rewriete + find where used
    public static function getListActiveTournamentsWithLeader() {

        $tournaments = Tournaments::find()
            ->where(['is_active' => Tournaments::GOING])
            ->asArray()
            ->all();

        foreach($tournaments as $k => &$tournament) {
            $forecasters = self::getForecastersWithPoints($tournament['id_tournament']);
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

    public function validForecast($attribute, $params) {

        if(($this->fscore_home == NULL && $this->fscore_guest != NULL) || ($this->fscore_home != NULL && $this->fscore_guest == NULL))
            $this->addError('fscore_guest', 'Неполный прогноз на матч '.$this->idGame->result->home_team. ' - '.$this->idGame->result->guest_team);
    }
}
