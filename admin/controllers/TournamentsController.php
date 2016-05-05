<?php

namespace app\admin\controllers;

use app\models\forecasts\Top3TeamsForecast;
use app\models\users\UsersTournaments;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use app\models\tournaments\Tournaments;
use app\models\tournaments\TournamentsSearch;
use app\models\tournaments\TeamTournaments;
use app\models\reminders\Reminders;
use app\models\forecasts\Forecasts;
use app\models\result\Result;
use app\models\teams\Teams;
use app\models\users\Users;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\db\IntegrityException;
use yii\filters\VerbFilter;

/**
 * TournamnetsController implements the CRUD actions for tournaments model.
 */
class TournamentsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'delete-participant' => ['post'],
                    'reminder' => ['post']
                ],
            ],
        ];
    }

    /**
     * Lists all tournaments models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Yii::$app->request->url;

        $searchModel = new TournamentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new tournaments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tournaments();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing tournaments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        Yii::$app->user->returnUrl = Yii::$app->request->url;
        $model = $this->findModel($id);

        //getting standings with participants
        $participants = new ArrayDataProvider([
            'allModels' => Result::getStandings($id),
            'pagination' => false,
        ]);

        //all teams(potential participants) for the current tournament
        $teams = Teams::getTeamCandidates($model->country0, TeamTournaments::getTournamentParticipantsID($id));

        $forecasters = new ArrayDataProvider([
            'allModels' => $model->getForecastersListNew(),
        ]);

        //getting the next tour number

        $nextTour = Tournaments::getNextTour($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        } else {
            return $this->render('update', compact('nextTour', 'model', 'teams', 'participants', 'forecasters'));
        }
    }

    //detailed information for tour forecast for the user
    public function actionTour($tour, $tournament, $user) {

        $forecastStatus = Forecasts::getTourUserForecastStatus($tour, $tournament, $user);

        $forecast = new ArrayDataProvider([
            'allModels' => $forecastStatus
        ]);

        return $this->renderAjax('tour', ['forecast' => $forecast]);
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

    //adding the participants to the tournament we edit
    public function actionAddParticipants() {

        $candidates = Yii::$app->request->post('candidates');

        if(!$candidates)
            return $this->goBack();
        
        $participants = Teams::find()
            ->where(['in', 'id_team', $candidates])
            ->all();

        $tournament = Tournaments::findOne(Yii::$app->request->post('tournament'));
        foreach($participants as $one) {

            $one->link('tournaments', $tournament);
        }

        return $this->goBack();
    }

    //delete participant from tournament
    public function actionDeleteParticipant($id) {

        try{
            $model = $this->findParticipantModel($id);
            $model->delete();

        } catch (IntegrityException $e) {
            Yii::$app->session->setFlash('error','Нельзя удалить команду из турнира, есть связанные игры/прогнозы');
        }

        return $this->goBack();
    }

    /**
     * Deletes an existing tournaments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try{
            $this->findModel($id)->delete();
        } catch (IntegrityException $e) {
            Yii::$app->session->setFlash('error','Нельзя удалить турнир, есть связанные команды');
        }

        return $this->redirect(['index']);
    }

    //sending reminders for the next tour in tournament
    public function actionReminder() {

        $tour = Yii::$app->request->post()['tour'];
        $tournament = Yii::$app->request->post()['tournament'];

        $sendReminders = Reminders::sendManualReminder($tour, $tournament);

        return $this->redirect(['tournaments/update', 'id' => $tournament]);

    }

    //filling in aliases for autoprocessing
    /**
     * @param $tournament
     * @var $trn Tournaments
     * @return string|\yii\web\Response
     */

    public function actionAlias($tournament) {

        //getting the list of teams for the tournament
        $teams = TeamTournaments::getTournamentParticipantsTeams($tournament);
        $trn = Tournaments::findOne($tournament);

        if (TeamTournaments::loadMultiple($teams, Yii::$app->request->post())) {
            foreach ($teams as $team) {
                $team->save(false);
            }
            return $this->redirect(['tournaments/update', 'id' => $trn->id_tournament]);
        }

        return $this->render('aliasAssign', compact('teams', 'trn'));
    }

    //autoprocess tournament data

    public function actionAutoprocess($id) {

        $tournament = $this->findModel($id);

        if($tournament->enableAutoprocess == 1 && isset($tournament->autoProcessURL)) {

            try {
                $tournament->autoProcess();
                Yii::$app->session->setFlash('status', 'Данные успешно загружены');
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            };
        }

        return $this->goBack();
    }

    /**
     * Finds the tournaments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return tournaments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tournaments::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @param $id
     * @return null|\app\models\tournaments\TeamTournaments
     * @throws NotFoundHttpException
     */
    //find teamtournament model to delete a participant
    protected function findParticipantModel($id)
    {
        if (($model = TeamTournaments::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
