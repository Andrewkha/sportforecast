<?php

namespace app\admin\controllers;

use Yii;
use app\models\teams\Teams;
use app\models\teams\TeamsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\db\IntegrityException;
use yii\filters\VerbFilter;
use yii\helpers\Html;


/**
 * TeamsController implements the CRUD actions for teams model.
 */
class TeamsController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all teams models.
     * @return mixed
     */
    public function actionIndex()
    {

        Yii::$app->user->returnUrl = Yii::$app->request->url;
        //getting list of countries

        $searchModel = new TeamsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new teams model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Teams();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing teams model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->goBack();
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing teams model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try{
            $model = $this->findModel($id);
            $model->delete();

        } catch (IntegrityException $e) {
            Yii::$app->session->setFlash('error','Нельзя удалить команду, есть связанные турниры/игры');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the teams model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return teams the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Teams::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
