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
            } catch (Exception $e) {
                Yii::error($one->tournament_name.' '.$e->getMessage(), 'console');
                continue;
            }
            Yii::info("Task Autoprocess for $one->tournament_name has been executed", 'console');
        }

        return 0;
    }

    public function actionGetNull()
    {

        $tournaments = Tournaments::getAutoprocessTournaments();

        foreach($tournaments as $one)
        {
            $teamTournament = TeamTournaments::find()
                ->where(['id_tournament' => $one->id_tournament])
                ->all();

            //getting Participant IDs for the tournament

            $teamIDs = ArrayHelper::getColumn($teamTournament, 'id');

            $games = Games::find()
                ->where(['or',['in', 'id_team_home', $teamIDs], ['in', 'id_team_guest', $teamIDs]])
                ->andWhere(
                    ['and',
                        ['or', ['not', ['score_home' => null]],['not', ['score_guest' => null]]],
                        ['or', ['points_home' => null],['points_guest' => null]]
                    ]
                )
                ->all();

            print_r($games);
        }
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
