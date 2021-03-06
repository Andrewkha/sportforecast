<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii\base\Exception;
use yii\console\Controller;
use Yii;
use app\models\tournaments\Tournaments;
use app\models\tournaments\TeamTournaments;
use app\models\result\Result;
use app\models\games\Games;
use app\models\reminders\Reminders;
use yii\helpers\ArrayHelper;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ConsoleController extends Controller
{

    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionAutoprocess()
    {
        $tournaments = Tournaments::getAutoprocessTournaments();

        foreach($tournaments as $one) {

            try {
                $one->autoProcess();
                Yii::info("Task Autoprocess for $one->tournament_name has been executed", 'console');
            } catch (Exception $e) {
                Yii::error($one->tournament_name.' '.$e->getMessage(), 'console');
                continue;
            }
        }

        return 0;
    }

    /*
     * get tours with wrong number of games - relevant for games schedule changes
     */

    public function actionToursWithWrongNumberGames()
    {
        $tournaments = Tournaments::getAutoprocessTournaments();

        foreach ($tournaments as $one)
        {
            if ($one->id_tournament === 23)
                continue;
            
            $games = Games::getGamesForTournament($one->id_tournament);

            $gamesPerTour = array_fill(1, $one->num_tours, 0);
            array_walk($games, function ($item) use (&$gamesPerTour) {
                $gamesPerTour[$item->tour]++;
            });

            $message = '';
            foreach ($gamesPerTour as $k => $element)
            {
                if($element != ($one->num_tours/4 + 1/2))
                    $message .=  $one->tournament_name.' '.$k. ' tour has wrong number of games' . "\r \n";
            }

            if(strlen($message) > 0)

                Yii::error($message, 'console');
        }
    }

    /**
     * get games with null score but not null points
     */
    public function actionGetNullPoints()
    {
        $games = Games::find()
            ->where(['score_home' => null])
            ->andWhere(['not',['points_home' => null]]);
        if(!$games->exists())
        {
            Yii::info('No games with null score but with positive points', 'console');
        }
            else
        {
            $gamesFound = $games->all();
            $this->getErrorMessage($gamesFound);
        }

        return 0;
    }
    /**
     * Finds games with empty points but with not empty score
     * @return int
     */
    public function actionGetNull()
    {

        $tournaments = Tournaments::getAutoprocessTournaments();

        $games = [];
        foreach($tournaments as $one)
        {
            $teamTournament = TeamTournaments::find()
                ->where(['id_tournament' => $one->id_tournament])
                ->all();

            //getting Participant IDs for the tournament

            $teamIDs = ArrayHelper::getColumn($teamTournament, 'id');

            $games = ArrayHelper::merge($games, Games::find()
                ->where(['or',['in', 'id_team_home', $teamIDs], ['in', 'id_team_guest', $teamIDs]])
                ->andWhere(
                    ['and',
                        ['or', ['not', ['score_home' => null]],['not', ['score_guest' => null]]],
                        ['or', ['points_home' => null],['points_guest' => null]]
                    ]
                )
                ->all()
            );
        }

        if(!empty($games))
        {
            $this->getErrorMessage($games);
        }
            else
        {
            Yii::info('No games with score but with no points', 'console');
        }

        return 0;
    }

    private function getErrorMessage($games)
    {
        $msg = '';
        foreach($games as $one)
        {
            $msg .= 'Issue with game ID: '.$one->id_game.', '.$one->competitors.', tour '.$one->tour.', tournament '.$one->tournament."\n\r";
        }

        Yii::error($msg, 'console');
    }


    //autoReminder
    //gel list of active tournaments
    //get recipients
    //two notifications with proper subjects
    //function UsersTournaments::autoreminderrecipients

    public function actionAutoreminder() {

        $tournaments = Tournaments::find()
            ->where(['not', ['is_active' => Tournaments::FINISHED]])
            ->all();

        foreach($tournaments as &$tournament) {

            if($tournament->is_active == Tournaments::NOT_STARTED && (($tournament->startsOn - time()) < 60*60*24*5)) {

                $tournament->is_active = Tournaments::GOING;
                $tournament->save(false);
            }

            $nextTour = Tournaments::getNextTour($tournament->id_tournament);

            if($nextTour != NULL) {

                $firstGameStarts = ArrayHelper::getValue(Result::find()
                        ->select(['min(dtime) as dtime'])
                        ->where(['id_tournament' => $tournament->id_tournament, 'tour' => $nextTour])
                        ->all()[0], 'dtime');

                if(($firstGameStarts > time() + 60*60*24*4 && $firstGameStarts < time() + 60*60*24*5) ||
                    ($firstGameStarts > time() + 60*60*24*2 && $firstGameStarts < time() + 60*60*24*3)) {

                        $sendReminders = Reminders::sendAutoReminder($nextTour, $tournament->id_tournament);
                        Yii::info("Task Autoreminder for $tournament->tournament_name $nextTour tour has been executed", 'console');
                }
            }
        }

        return 0;
    }
}
