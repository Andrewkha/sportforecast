<?php

namespace app\admin;

use yii\filters\AccessControl;

class module extends \yii\base\Module
{
    public $controllerNamespace = 'app\admin\controllers';

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function behaviors() {

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['administrator'],
                    ]
                ]
            ]
        ];
    }
}
