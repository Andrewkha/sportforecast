<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 6/4/15
 * Time: 8:57 PM
 * To change this template use File | Settings | File Templates.
 */

namespace app\controllers;

use yii\web\NotFoundHttpException;
use yii\data\Pagination;
use yii\web\Controller;
use app\models\news\News;
use app\models\tournaments\Tournaments;
use Yii;

class NewsController extends Controller{

    public function actionIndex() {

        //tournament filter initialization
        $tournamentFilter = News::activeTournamentFilter();

        $query = News::find()
                ->where(['archive' => News::ARCHIVE_FALSE])
                ->with(['tournament', 'author0'])
                ->orderBy(['date' => SORT_DESC]);

        if(Yii::$app->request->post() && Yii::$app->request->post('tournamentFilter') != 'all') {

            $query->andWhere(['id_tournament' => Yii::$app->request->post('tournamentFilter')]);
            $selected = Yii::$app->request->post('tournamentFilter');
        } elseif (Yii::$app->request->post() && Yii::$app->request->post('tournamentFilter') == 'all') {
            $selected = 'all';
        } else {
            $selected = null;
        }

        $countQuery = clone $query;
        $pages = new Pagination([
            'totalCount' => $countQuery->count(),
            'pageSize' => 10,
        ]);

        $news = $query->offset($pages->offset)
                ->limit($pages->limit)
                ->all();

        return $this->render('index', compact('news', 'pages', 'tournamentFilter', 'selected'));

    }

    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}