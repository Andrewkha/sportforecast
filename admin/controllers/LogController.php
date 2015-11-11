<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 8/23/2015
 * Time: 5:24 PM
 */

namespace app\admin\controllers;

use app\models\log\Log;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class LogController extends Controller
{

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Log::find()->andFilterWhere(['category' => Log::CATEGORY_CONSOLE]),
            'sort' => [
                'defaultOrder' => ['log_time' => SORT_DESC]
            ]
        ]);
        return $this->render('index', ['dataProvider' => $dataProvider]);
    }
}