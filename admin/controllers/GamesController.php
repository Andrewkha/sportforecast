<?php

namespace app\admin\controllers;

use app\models\tournaments\TeamTournaments;
use Yii;
use app\models\games\Games;
use app\models\games\GamesUploadForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\tournaments\Tournaments;
use yii\db\IntegrityException;
use yii\web\UploadedFile;


/**
 * GamesController implements the CRUD actions for games model.
 */
class GamesController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'save' => ['post'],
                    'update' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex($tournament) {

        Yii::$app->user->returnUrl = Yii::$app->request->url;

        $curr_tournament = Tournaments::findOne($tournament);
        if(Yii::$app->request->post('tours')) {
            $tours = Yii::$app->request->post('tours');
        } else {
            $tours = range(1, $curr_tournament->num_tours, 1);
        }
        $tourGames = Games::getGamesGroupedByTour($tournament, $tours);

        for($i =1 ; $i <= $curr_tournament->num_tours; $i++)
            $tour_list[$i] = "$i тур";

        //preparing model for creating a game
        $newGame = new Games;
        $participants = TeamTournaments::getTournamentParticipantsTeams($tournament);

        //model for uploading games from Excel
        $file = new GamesUploadForm();

        if(Yii::$app->request->post('Games')) {

            $updatedGames = Yii::$app->request->post('Games');

            foreach($updatedGames as $k => $game) {
                $models[$k] = $this->findModel($k);
            }

            if(Games::loadMultiple($models, Yii::$app->request->post())) {

                $error = '';

                foreach($models as $model) {
                    if (!$model->save()) {
                        $error = $error.'Ошибка сохранения игры '.$model->competitors."<br>";
                    }
                }

                if($error === '') {
                    Yii::$app->session->setFlash('status', 'Записи сохранены успешно');
                } else {
                    Yii::$app->session->setFlash('error', $error);
                }
                return $this->goBack();
            }
        }

        return $this->render('index2', compact('tourGames', 'curr_tournament', 'tour_list', 'newGame', 'participants', 'file'));
    }

    /**
     * Creates a new Games model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionAdd()
    {

        $model = new Games();
        $model->scenario = 'addGame';

        if($model->load(Yii::$app->request->post())) {
            if($model->save())
                Yii::$app->session->setFlash('status','Игра добавлена');
            else
                Yii::$app->session->setFlash('error',$model->getFirstError('id_team_home'));
        } else {
            Yii::$app->session->setFlash('error','Ошибка');
        }

        return $this->goBack();
    }


    /**
     * Deletes an existing games model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)

    {
        try{
            $this->findModel($id)->delete();
            Yii::$app->session->setFlash('status','Запись успешно удалена');
        } catch (IntegrityException $e) {
            Yii::$app->session->setFlash('error','Нельзя удалить игру, на которую есть прогнозы');
        }

        return $this->goBack();
    }

    public function actionUpload() {

        $model = new GamesUploadForm();
        $file = UploadedFile::getInstance($model, 'file');

        $upload = Games::uploadExcel($file->tempName);

        if(isset($upload['success']))
            Yii::$app->session->setFlash('status',$upload['success']);
        elseif(isset($upload['failure']))
            Yii::$app->session->setFlash('error',$upload['failure']);

        return $this->goBack();
    }

    /**
     * Finds the games model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return games the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Games::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
