<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/30/2015
 * Time: 5:50 PM
 */

namespace app\models\traits;

use Yii;
use app\models\users\UsersTournaments;
use app\models\forecasts\Forecasts;
use app\models\tournaments\Tournaments;
use app\models\result\Result;
use app\models\reminders\Reminders;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

trait remindersTrait
{
    //tries to find the record for specific user, tournament, tour. If finds, returns it, otherwise creates new object
    public static function getReminder($user, $tournament, $tour) {

        $model = self::findOne(['user' => $user, 'tournament' => $tournament, 'tour' => $tour]);

        if($model)
            return $model;
        else {
            $model = new Reminders();
            $model->tour = $tour;
            $model->tournament = $tournament;
            $model->user = $user;

            return $model;
        }
    }

    public static function sendManualReminder($tour, $tournament) {

        $users = UsersTournaments::getReminderRecipients($tournament, $tour);
        if(!empty($users)) {
            return self::sendReminder($tour, $tournament, $users);
        } else {
            throw new ErrorException('Error sending reminders');
        }
    }

    public static function sendAutoReminder($tour, $tournament) {

        $users = UsersTournaments::getAutoReminderRecipients($tournament, $tour);
        if(!empty($users)) {
            return self::sendReminder($tour, $tournament, $users);
        } else {
            throw new ErrorException('Error sending reminders');
        }
    }

    //sending forecast reminders
    private static function sendReminder($tour, $tournament, $users) {

        $subject = "Напоминание: сделайте прогноз на матчи $tour тура турнира ".Tournaments::findOne($tournament)->tournament_name;

        $firstGameStarts = date('d-m-Y в H:i', ArrayHelper::getValue(Result::find()
            ->select(['min(dtime) as dtime'])
            ->where(['id_tournament' => $tournament, 'tour' => $tour])
            ->all()[0], 'dtime')
        );

        foreach($users as $user) {

            if(Forecasts::getUserForecastTour($user->id, $tournament)[$tour] == 0) {
                $description = "Вы не сделали прогноз на матчи $tour тура турнира ".Tournaments::findOne($tournament)->tournament_name.". Поторопитесь, первая игра тура начинается <strong>$firstGameStarts</strong>";
            } else {
                $description = "Вы сделали прогноз не на все матчи: $tour тура турнира ".Tournaments::findOne($tournament)->tournament_name.". Поторопитесь, первая игра тура начинается $firstGameStarts";
            }

            $content = new ArrayDataProvider([
                'allModels' => Forecasts::getTourUserForecastStatus($tour, $tournament, $user->id),
                'sort' => false,
                'pagination' => false,
            ]);


            $messages[] = Yii::$app->mailer->compose('reminder', [
                'content' => $content,
                'description' => $description,
                'user' => $user,
            ])
                ->setFrom([Yii::$app->params['adminEmail'] => 'Sportforecast'])
                ->setTo($user->email)
                ->setSubject($subject);

            //adding a record to the DB
            $reminder = self::getReminder($user->id, $tournament, $tour);
            $reminder->save();
        }

        if(!empty($messages)) {
            Yii::$app->mailer->sendMultiple($messages);
        }

        return true;
    }
}