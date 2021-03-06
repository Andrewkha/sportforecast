<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 6/4/15
 * Time: 8:57 PM
 * To change this template use File | Settings | File Templates.
 */

namespace app\controllers;

use app\models\forecasts\Top3TeamsForecast;
use app\models\forecasts\TopTeamsForm;
use app\models\tournaments\TeamTournaments;
use yii\web\NotFoundHttpException;
use app\models\users\UsersTournaments;
use app\models\countries\Countries;
use app\models\tournaments\Tournaments;
use app\models\tournaments\TournamentsSearch;
use app\models\forecasts\Forecasts;
use app\models\users\Users;
use app\models\result\Result;
use app\models\games\Games;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\filters\VerbFilter;
use Yii;

class TournamentsController extends Controller{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['participate', 'notification', 'delete'],
                'denyCallback' => function($rule, $action) {
                    return $this->goHome();
                },
                'rules' => [
                    [
                        'actions' => ['participate', 'notification','delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'participate' => ['post'],
                    'notification' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex() {

        if(Yii::$app->user->isGuest) {

            $dataProvider = new ArrayDataProvider([
                'allModels' => Tournaments::getAllTournaments()
            ]);
            $dataProvider->sort = [
                'attributes' => ['startsOn'],
                'defaultOrder' => [
                    'startsOn' => SORT_DESC
                ]
            ];
            $dataProvider->pagination = [
                'pageSize' => 10,
            ];

            return $this->render('indexGuest', [
                'dataProvider' => $dataProvider,
            ]);
        }

        $user = Yii::$app->user->identity;

        Yii::$app->user->returnUrl = Yii::$app->request->url;

        //active tournaments where user participates
        $k = Tournaments::getActivePendingTournamentsUserParticipate($user->id);
        $userTournaments = new ArrayDataProvider([
            'allModels'=> $k,
            'sort' => [
                'attributes' => ['startsOn'],
                'defaultOrder' => [
                    'startsOn' => SORT_DESC
                ]
            ]
        ]);

        $k = Tournaments::finishedTournamentsUserParticipated($user->id);
        $userFinishedTournaments = new ArrayDataProvider([
            'allModels'=> $k,
            'sort' => [
                'attributes' => ['startsOn'],
                'defaultOrder' => [
                    'startsOn' => SORT_DESC
                ]
            ]
        ]);

        $k = Tournaments::getAllTournamentsUserNotParticipate($user->id);
        //all tournaments, those not finished - ability to start participating
        $notUserTournaments = new ArrayDataProvider([
            'allModels'=> $k,
            'sort' => [
                'attributes' => ['startsOn'],
                'defaultOrder' => [
                    'startsOn' => SORT_DESC
                ]
            ]
        ]);

        return $this->render('indexUser', [
            'userTournaments' => $userTournaments,
            'userFinishedTournaments' => $userFinishedTournaments,
            'notUserTournaments' => $notUserTournaments,
        ]);
    }

    public function actionDetails($id) {

        $user = Yii::$app->user;
        Yii::$app->user->returnUrl = Yii::$app->request->url;

        $tournament = Tournaments::findOne($id);

        if($tournament === null) {
            throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
        }

        $teamParticipants = new ArrayDataProvider([
            'allModels' => Result::getStandings($id),
            'pagination' => false,
        ]);

        //games list prep
        if(Yii::$app->request->post('tours')) {
            $tours = Yii::$app->request->post('tours');
        } else {
            $tours = range(1, $tournament->num_tours, 1);
        }

        for($i =1 ; $i <= $tournament->num_tours; $i++)
            $tour_list[$i] = "$i тур";

        $forecasters = new ActiveDataProvider([
            'query' => UsersTournaments::find()->forecastersStandings($id),
            'pagination' => false,

        ]);

        //if guest or not participating in the tournament
        if($user->isGuest || !$user->identity->isUserParticipatesInTournament($id)) {

            $tourGames = Games::getGamesGroupedByTour($id, $tours);
            $viewFile = 'tournamentDetailsGuest';
            $winners = [];
            $forecastedStandings = [];
        } else {

            $forecastedStandings = new ArrayDataProvider([
                'allModels' => Result::getForecastedStandings($id, $user->id),
                'pagination' => false,
            ]);
            $tourGames = Games::getGamesGroupedByTourWithForecast($id, $tours, $user->id);
            $viewFile = 'tournamentDetailsUser';
            
            //preparing tournament winners forecast form
            $winners = new TopTeamsForm($user->id, $id);
            if ($winners->load(Yii::$app->request->post()) && $winners->validate()) {

                if(time() < $tournament->wfDueTo)
                {
                    $winners->edit();
                    Yii::$app->getSession()->setFlash('success', 'Прогноз на призеров турнира успешно сохранен');
                } else
                    Yii::$app->getSession()->setFlash('success', 'Не нужно пытаться обмануть :)');
                return $this->refresh();
            }
        }

        $data = compact('tournament', 'teamParticipants', 'forecasters', 'tour_list', 'tourGames', 'winners', 'forecastedStandings');

        if($tournament->is_active == Tournaments::FINISHED)
        {
            $data['additionalPoints'] = implode('</br>', Top3TeamsForecast::getClarifications($user->id, $id));
            $userTournamentsModel = UsersTournaments::find()
                ->where(['id_tournament' => $id])
                ->andWhere(['id_user' => $user->id])
                ->with('winnersForecast')
                ->one();

            if(!empty($userTournamentsModel))
                $data['totalAdditionalPoints'] = $userTournamentsModel->calculateAdditionalPoints();
        }

        else
        {
            $data['additionalPoints'] = '';
            $data['totalAdditionalPoints'] = '';
        }

        return $this->render($viewFile, $data);

    }

    //detailed information for tournament forecast for the user
    public function actionUser($user, $tournament) {

        $userModel = Users::findOne($user);
        $forecastStatus = Forecasts::getUserForecastStatus($tournament, $user);

        $forecast = new ArrayDataProvider([
            'allModels' => $forecastStatus,
            'pagination' => false,
        ]);

        $winnersForecast = Top3TeamsForecast::find()
            ->where(['id_tournament' => $tournament])
            ->andWhere(['id_user' => $user])
            ->with('team.idTeam')
            ->orderBy(['forecasted_position' => SORT_ASC])
            ->asArray()
            ->all();

        $isFinished = Tournaments::findOne($tournament)->is_active == Tournaments::FINISHED;

        $winnersForecastDataProvider = new ArrayDataProvider([
            'allModels' => $winnersForecast,
            'pagination' => false,
        ]);

        $data = ['forecast' => $forecast,
            'user' => $userModel,
            'winnersForecast' => $winnersForecastDataProvider,
            'isFinished' => $isFinished,
        ];

        if($isFinished)
        {
            $userTournamentModel = UsersTournaments::find()
                ->where(['id_user' => $user])
                ->andWhere(['id_tournament' => $tournament])
                ->with('winnersForecast')
                ->one();

            $data['winnersForecastDetails'] = implode('</br>', Top3TeamsForecast::getClarifications($user, $tournament));
            $data['totalAdditionalPoints'] = $userTournamentModel->calculateAdditionalPoints();
        }

        return $this->renderAjax('user', $data);

    }

    public function actionGames($id) {

        $games = new ArrayDataProvider([
            'allModels' => Result::getParticipantGames($id),
            'pagination' => false,
        ]);

        $tournament = TeamTournaments::findOne($id);

        $team = TeamTournaments::findOne($id);

        return $this->render('gamesGuest', compact('games', 'tournament', 'team'));
    }

    public function actionParticipate($id) {

        $tournament = Tournaments::findOne($id);

        if(UsersTournaments::find()->where(['id_tournament' => $id])->andWhere(['id_user' => Yii::$app->user->id])->exists()) {

            Yii::$app->session->setFlash('success', "Вы уже участвуете в турнире $tournament->tournament_name");

            return $this->goBack();
        }

        $model = new UsersTournaments();
        $model->id_tournament = $id;
        $model->id_user = Yii::$app->user->id;

        $model->save();

        Yii::$app->session->setFlash('success', "Вы теперь участвуете в турнире $tournament->tournament_name");

        return $this->goBack();
    }

    public function actionNotification() {

        $post = Yii::$app->request->post();

        $keys = array_keys($post['UsersTournaments']);

        $models = UsersTournaments::find()
            ->where(['in', 'id', $keys])
            ->indexBy('id')
            ->all();

        if(UsersTournaments::loadMultiple($models, $post)) {
            foreach($models as $model)
                $model->save(false);

            Yii::$app->session->setFlash('success', 'Параметры уведомлений успешно сохранены');
        }

        return $this->goBack();
    }

    public function actionDelete($id) {

        $model = UsersTournaments::findOne(['id_tournament' => $id, 'id_user' => Yii::$app->user->id]);

        //deleting forecasts
        if($model) {

            $model->deleteForecasts();
            $model->delete();
            Yii::$app->session->setFlash('success', 'Участие отменено, все прогнозы удалены');

        }

        return $this->goBack();

    }
}