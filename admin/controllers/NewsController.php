<?php

namespace app\admin\controllers;

use Yii;
use app\models\news\News;
use app\models\news\NewsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\IntegrityException;


/**
 * NewsController implements the CRUD actions for news model.
 */
class NewsController extends Controller
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
     * Lists all news models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Yii::$app->request->url;

        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $dataProvider->sort->defaultOrder = ['date' => SORT_DESC];
        $dataProvider->pagination->pageSize= 5;

        //archive filter array creation
        $archiveFilter = News::getStatuses();
        //$archiveFilter = [News::ARCHIVE_TRUE => News::archive(News::ARCHIVE_TRUE), News::ARCHIVE_FALSE => News::archive(News::ARCHIVE_FALSE)];

        //tournament filter initialization
        $tournamentFilter = News::tournamentFilter();

        //author filter initialization

        $authorFilter = News::authorFilter();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'archive' => $archiveFilter,
            'tournament' => $tournamentFilter,
            'author' => $authorFilter,
        ]);
    }


    /**
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();

        //tournaments dropdown initialization
        $tournaments = News::listDropDownPrep();

        if ($model->load(Yii::$app->request->post())) {

            $model->save();
            Yii::$app->session->setFlash('success', 'Новость успешно добавлена');
            return $this->goBack();
        }

        return $this->render('create', [
            'model' => $model,
            'tournaments' => $tournaments,
        ]);
    }

    /**
     * Updates an existing news model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $tournaments = News::listDropDownPrep();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if($model->isAttributeChanged('body') || $model->isAttributeChanged('subject')) {

                $model->save();
                Yii::$app->session->setFlash('success', 'Новость успешно изменена');
                return $this->goBack();
            }
        }

        return $this->render('update', [
            'model' => $model,
            'tournaments' => $tournaments,
        ]);

    }

    /**
     * Deletes an existing news model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            Yii::$app->session->setFlash('success','Новость удалена успешно');
        } catch (IntegrityException $e) {
            Yii::$app->session->setFlash('error','Нельзя удалить новость');
        }

        return $this->goBack();
    }

    public function actionArchive() {

        $input = Yii::$app->request->post();

        foreach($input['News'] as $k => &$one) {
            $news[$k] = $this->findModel($k);
            $one['archive'] = (int)$one['archive'];
        }

        if(News::loadMultiple($news, $input)) {

            foreach($news as $one) {

                $one->setArchive();
            }
            Yii::$app->session->setFlash('success', 'Статус новостей изменен');

        } else {
            Yii::$app->session->setFlash('error', 'Ошибка изменения статуса');
        }
        return $this->goBack();
    }

    /**
     * Finds the news model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return news the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
