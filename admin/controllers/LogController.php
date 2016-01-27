<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 8/23/2015
 * Time: 5:24 PM
 */

namespace app\admin\controllers;

use Yii;
use app\models\log\Log;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class LogController extends Controller
{

    public function actionIndex()
    {

        $typeFilter = Log::getStatusesArray();

        $searchModel = new Log();

        $searchModel->load(Yii::$app->request->queryParams);

        $query = Log::find();
        $query->andFilterWhere([
            'level' => $searchModel->level,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query->andFilterWhere(['category' => Log::CATEGORY_CONSOLE]),
            'sort' => [
                'defaultOrder' => ['log_time' => SORT_DESC]
            ]
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel, 'typeFilter' => $typeFilter]);
    }
}