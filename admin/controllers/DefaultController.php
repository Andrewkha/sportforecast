<?php

namespace app\admin\controllers;

use yii\web\Controller;

class DefaultController extends Controller
{

    public $items = ['countries' => 'Страны', 'teams' => 'Команды', 'tournaments' => 'Турниры', 'news' => 'Новости', 'users' => 'Пользователи', 'log' => 'Журнал'];

    public function actionIndex()
    {
        return $this->render('index', ['items' => $this->items]);
    }
}
