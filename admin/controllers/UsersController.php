<?php

namespace app\admin\controllers;

use Yii;
use app\models\users\Users;
use app\models\users\UsersSearch;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\users\UsersTournaments;
use yii\helpers\ArrayHelper;


/**
 * UsersController implements the CRUD actions for users model.
 */
class UsersController extends Controller
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
     * Lists all users models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Yii::$app->request->url;
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Updates an existing users model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $rbac = \Yii::$app->authManager;
        $allRoles = $rbac->getRoles();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            foreach(Yii::$app->request->post()['role'] as $k => $one) {
                if($one == 0 && $rbac->checkAccess($id, $k))
                    $rbac->revoke($rbac->getRole($k), $id);
                elseif($one == 1 && !$rbac->checkAccess($id, $k))
                    $rbac->assign($rbac->getRole($k), $id);
            }
            return $this->goBack();
        } else {

            //preparing tournament participation data
            $tournaments = $model->getUsersTournaments()->with('idTournament.country0')->all();

            //getting leader, leader points, user position and points for each tournament
            foreach($tournaments as $tournament) {
                $tournament->getUserLeader();
            }

            //converting tournaments to the arraydata provider
            $arrayDataProvider = new ArrayDataProvider([
                'allModels' => $tournaments,
            ]);

            return $this->render('update', [
                'model' => $model,
                'allRoles' => $allRoles,
                'tournaments' => $arrayDataProvider,
            ]);
        }
    }

    /**
     * Deletes an existing users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
