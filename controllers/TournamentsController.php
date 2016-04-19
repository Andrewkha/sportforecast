<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 6/4/15
 * Time: 8:57 PM
 * To change this template use File | Settings | File Templates.
 */

namespace app\controllers;

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

            $countries = Countries::find()->orderBy('country', 'asc')->asArray()->all();
            $countries_list = ArrayHelper::map($countries, 'id', 'country');

            $searchModel = new TournamentsSearch();
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
            $dataProvider->sort = [
                'attributes' => ['startsOn', 'is_active', 'tournament_name'],
                'defaultOrder' => [
                    'startsOn' => SORT_DESC
                ]
            ];
            $dataProvider->pagination = [
                'pageSize' => 10,
            ];

            return $this->render('indexGuest', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'countries' => $countries_list,
            ]);
        }

        $user = Yii::$app->user->identity;
        Yii::$app->user->returnUrl = Yii::$app->request->url;
        //active tournaments where user participates
        $userTournaments = new ArrayDataProvider([
            'allModels'=> UsersTournaments::getActivePendingUserTournamentsAndPosition($user->id),
            'sort' => [
                'attributes' => ['idTournament.startsOn'],
                'defaultOrder' => [
                    'idTournament.startsOn' => SORT_DESC
                ]
            ]
        ]);

        $userFinishedTournaments = new ArrayDataProvider([
            'allModels'=> UsersTournaments::getFinishedUserTournamentsAndPosition($user->id),
            'sort' => [
                'attributes' => ['idTournament.startsOn'],
                'defaultOrder' => [
                    'idTournament.startsOn' => SORT_DESC
                ]
            ]
        ]);

        //all tournaments, those not finished - ability to start participating
        $notUserTournaments = new ArrayDataProvider([
            'allModels'=> Tournaments::getAllTournamentsNotParticipate($user->id),
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

        $forecasters = new ArrayDataProvider([
            'allModels' => $tournament->getForecastersList(),
            'pagination' => false,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        //if guest or not participating in the tournament
        if($user->isGuest || !$user->identity->isUserParticipatesInTournament($id)) {

            $tourGames = Games::getGamesGroupedByTour($id, $tours);
            $viewFile = 'tournamentDetailsGuest';
        } else {

            $tourGames = Games::getGamesGroupedByTourWithForecast($id, $tours, Yii::$app->user->id);
            $viewFile = 'tournamentDetailsUser';
        }

        return $this->render($viewFile, compact('tournament', 'teamParticipants', 'forecasters', 'tour_list', 'tourGames'));

    }

    //detailed information for tournament forecast for the user
    public function actionUser($user, $tournament) {

        $forecastStatus = Forecasts::getUserForecastStatus($tournament, $user);

        $forecast = new ArrayDataProvider([
            'allModels' => $forecastStatus,
            'pagination' => false,
        ]);

        $user = Users::findOne($user);

        return $this->renderAjax('user', ['forecast' => $forecast, 'user' => $user]);
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

        $keys = ArrayHelper::getColumn($post['UsersTournaments'], 'id_tournament');

        $models = UsersTournaments::find()
            ->where(['and', ['in', 'id_tournament', $keys], ['id_user' => Yii::$app->user->id]])
            ->indexBy('id_tournament')
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