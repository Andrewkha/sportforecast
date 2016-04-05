<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/30/2015
 * Time: 5:29 PM
 */

namespace app\models\traits;

use app\models\reminders\Reminders;
use app\models\forecasts\Forecasts;
use app\models\tournaments\Tournaments;
use app\models\users\Users;
use app\models\result\Result;
use yii\helpers\ArrayHelper;


trait usersTournamentsTrait
{
    //calculate user points, user position, leader points and leader name
    public function getUserLeader() {

        //getting the list of games for tournament
        $games = Result::find()->select('id_game')->where(['id_tournament' => $this->id_tournament])->all();

        $standings = Forecasts::find()
            ->select(['sf_forecasts.id_user', 'sum(points) as points'])
            ->where(['in', 'id_game', ArrayHelper::getColumn($games, 'id_game')])
            ->groupBy('id_user')->orderBy(['points' => SORT_DESC])
            ->all();

        if($standings) {
            $this->leader = $standings[0]->idUser->username;
            $this->leaderPoints = $standings[0]->points;
        }

        $result = ArrayHelper::map($standings, 'id_user', 'points');
        $this->userPoints = ArrayHelper::getValue($result, $this->id_user);

        foreach($standings as $k => $one) {

            if($one->id_user == $this->id_user) {
                $this->userPosition = $k+1;
            }
        }

        if(empty($this->userPoints))
            $this->userPoints = '-';

        if(empty($this->userPosition))
            $this->userPosition = '-';

        if(empty($this->leader))
            $this->leader = '-';

        if(empty($this->leaderPoints))
            $this->leaderPoints = '-';

        return true;
    }

    //getting list of users who subscribed for the tournament notifications and didn't make forecast for the tour provided. In other words reminder recipients
    public static function getReminderRecipients($tournament, $tour) {

        $candidates = self::find()
            ->joinWith('idUser')
            ->where(['id_tournament' => $tournament, 'notification' => self::NOTIFICATION_ENABLED, 'active' => Users::STATUS_ACTIVE])
            ->all();

        $recipients = [];

        foreach($candidates as $one) {

            if(Forecasts::getUserForecastTour($one['id_user'], $tournament)[$tour] != '2') {

                $recipients[] = Users::find()
                    ->where(['id' => $one['id_user']])
                    ->one();
            }
        }

        return $recipients;
    }

    //getting list of users who subscribed for the tournament notifications and didn't make forecast for the tour provided. In other words reminder recipients
    public static function getAutoReminderRecipients($tournament, $tour) {

        $candidates = self::find()
            ->joinWith('idUser')
            ->where(['id_tournament' => $tournament, 'notification' => self::NOTIFICATION_ENABLED, 'active' => Users::STATUS_ACTIVE])
            ->all();

        $recipients = [];

        foreach($candidates as $one) {

            if(Forecasts::getUserForecastTour($one['id_user'], $tournament)[$tour] != '2' && Reminders::ifEligible($tournament, $tour, $one['id_user'])) {

                $recipients[] = Users::find()
                    ->where(['id' => $one['id_user']])
                    ->one();
            }
        }

        return $recipients;
    }

    //getting position and points for every active tournament user participates in (for showing on the main page)

    public static function getActiveUserTournamentsAndPosition($user) {

        $tournaments = self::find()
            ->joinWith(['idTournament'])
            ->where(['is_active' => Tournaments::GOING])
            ->andWhere(['id_user' => $user])
            ->all();

        foreach($tournaments as &$tournament)
            $tournament->getUserLeader();

        return $tournaments;
    }

    public static function getActivePendingUserTournamentsAndPosition($user) {

        $tournaments = self::find()
            ->joinWith(['idTournament'])
            ->where(['or', ['is_active' => Tournaments::GOING], ['is_active' => Tournaments::NOT_STARTED]])
            ->andWhere(['id_user' => $user])
            ->all();

        foreach($tournaments as &$tournament)
            $tournament->getUserLeader();

        return $tournaments;
    }

    public static function getFinishedUserTournamentsAndPosition($user) {

        $tournaments = self::find()
            ->joinWith(['idTournament'])
            ->where(['is_active' => Tournaments::FINISHED])
            ->andWhere(['id_user' => $user])
            ->all();

        foreach($tournaments as &$tournament)
            $tournament->getUserLeader();

        return $tournaments;
    }

    //get all user tournaments and position

    public static function getAllUserTournamentsAndPosition($user) {

        $tournaments = self::find()
            ->joinWith(['idTournament'])
            ->andWhere(['id_user' => $user])
            ->all();

        foreach($tournaments as &$tournament)
            $tournament->getUserLeader();

        return $tournaments;
    }

    public static function getTournamentsUserParticipates($user)
    {
        $tournaments = self::find()
            ->where(['id_user' => $user])
            ->all();

        return $tournaments;
    }

    public static function isUserParticipate($user, $tournament) {

        return self::find()
            ->where(['and', ['id_user' => $user], ['id_tournament' => $tournament]])
            ->exists();
    }
}