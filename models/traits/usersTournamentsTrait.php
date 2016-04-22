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
use app\models\games\Games;
use app\models\result\Result;
use yii\helpers\ArrayHelper;


trait usersTournamentsTrait
{
    //getting list of users who subscribed for the tournament notifications and didn't make forecast for the tour provided. In other words reminder recipients
    public static function getReminderRecipients($tournament, $tour) {

        $candidates = self::find()
            ->joinWith('idUser')
            ->where(['id_tournament' => $tournament, 'notification' => self::NOTIFICATION_ENABLED, 'active' => Users::STATUS_ACTIVE])
            ->all();

        $recipients = [];
        $tours =  Games::getNumberOfGamesPerTour($tournament);
        foreach($candidates as $one) {

            if(Forecasts::getUserForecastTour($one['id_user'], $tournament, $tours)[$tour] != '2') {

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
        $tours =  Games::getNumberOfGamesPerTour($tournament);
        foreach($candidates as $one) {

            if(Forecasts::getUserForecastTour($one['id_user'], $tournament, $tours)[$tour] != '2' && Reminders::ifEligible($tournament, $tour, $one['id_user'])) {

                $recipients[] = Users::find()
                    ->where(['id' => $one['id_user']])
                    ->one();
            }
        }

        return $recipients;
    }

    public static function isUserParticipate($user, $tournament) {

        return self::find()
            ->where(['and', ['id_user' => $user], ['id_tournament' => $tournament]])
            ->exists();
    }
}