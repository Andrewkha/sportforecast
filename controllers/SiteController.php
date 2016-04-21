<?php

namespace app\controllers;

use app\models\users\Users;
use app\models\users\UsersTournaments;
use Yii;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\users\LoginForm;
use app\models\users\SignupForm;
use app\models\users\passResetForm;
use app\models\users\RenewPasswordForm;
use yii\web\BadRequestHttpException;
use yii\base\InvalidParamException;
use app\models\ContactForm;
use app\models\users\ProfileForm;
use app\models\forecasts\Forecasts;
use app\models\games\Games;
use app\models\tournaments\Tournaments;
use yii\helpers\Html;


class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['profile', 'logout', 'login', 'signup', 'passreset', 'reset-password', 'rehash'],
                'denyCallback' => function($rule, $action) {
                    return $this->goHome();
                },
                'rules' => [
                    [
                        'actions' => ['logout', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],

                    [
                        'actions' => ['login', 'signup', 'passreset', 'reset-password', 'rehash'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }


    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'testLimit' => 1,
                'fontFile' => '@yii/captcha/arial.ttf',
                'offset' => 0,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionIndex()
    {
        Yii::$app->user->returnUrl = Yii::$app->request->url;

        if(Yii::$app->user->isGuest) {

            //list of active tournaments
            $tournaments = new ArrayDataProvider([
                'allModels' => Tournaments::activePendingTournamentsWithLeader()
            ]);

            $futureGames = Games::getAllFutureGames();
            $recentGames = Games::getAllRecentGames();

            return $this->render('indexGuest', compact('tournaments', 'futureGames', 'recentGames', 'news'));
        }

        //list of active tournaments
        $tournaments = new ArrayDataProvider([
            'allModels' => Tournaments::getActivePendingTournamentsNotParticipate(Yii::$app->user->id),
        ]);

        $futureGames = Games::getAllFutureGamesWithForecast(Yii::$app->user->id);
        $recentGames = Games::getAllRecentGamesWithForecast(Yii::$app->user->id);

        $userTournaments = new ArrayDataProvider([
            'allModels' => UsersTournaments::getActivePendingUserTournamentsAndPosition(Yii::$app->user->id),
        ]);

        return $this->render('indexUser', compact('tournaments', 'userTournaments', 'futureGames', 'recentGames'));
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {

            Yii::$app->session->setFlash('success', 'С возвращением!!!');
            return $this->goHome();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /*
    public function actionRehash() {

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RehashFrom();
        if ($model->load(Yii::$app->request->post()) && $model->rehash()) {
            return $this->goBack();
        } else {
            return $this->render('rehash', [
                'model' => $model,
            ]);
        }
    }
    */

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup() {

        $model = new SignupForm();

        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->register()) {

                Yii::$app->session->setFlash('success', 'Поздравляем с успешной регистрацией!');

                return $this->goHome();
        } else {

            return $this->render('signup', [
                'model' => $model,
            ]);
        }
    }

    //password reset token request
    public function actionPassreset() {

        $model = new passResetForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if($model->passReset()) {
                Yii::$app->session->setFlash('success', 'Ваш запрос обработан. Проверьте электронную почту для получения дальнейших инструкций');

                return $this->goHome();
            } else {

                Yii::$app->session->setFlash('error', 'При сбросе пароля произошла ошибка. Обратитесь к '.Html::mailto('администратору', Yii::$app->params['adminEmail']).' за поддержкой');
            }
        }

        return $this->render('passreset', [
                'model' => $model,
        ]);

    }

    //actual password reset
    public function actionResetPassword($token) {

        try {
            $model = new RenewPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'Новый пароль успешно сохранен');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionProfile() {

        $model = new ProfileForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->edit()) {

            Yii::$app->getSession()->setFlash('success', 'Профиль успешно сохранен');

            return $this->goHome();
        }

        return $this->render('profile', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|\yii\web\Response
     * @var $user Users
     */
    public function actionContact()
    {
        $model = new ContactForm();

        if($user = Yii::$app->user->identity) {
            $model->name = $user->username;
            $model->email = $user->email;
        }
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionForecastSave() {

        $forecasts = Yii::$app->request->post('forecasts');

        foreach($forecasts as $k => $one) {

            if($one['fscore_home'] == NULL)
                continue;
            $model = $this->getForecastModel($k);
            $model->fscore_home = $one['fscore_home'];
            $model->fscore_guest = $one['fscore_guest'];

            if(!$model->validate()) {
                $errors = 'В вашем прогнозе была ошибка';
            } else {
                $model->save(false);
            }
        }

        if(isset($errors)) {
            Yii::$app->session->setFlash('error', $errors);
        } else {
            Yii::$app->session->setFlash('success', 'Прогноз успешно сохранен');
        }

        return $this->goBack();
    }

    private function getForecastModel($idGame) {

        $model = Forecasts::findOne(['id_game' => $idGame, 'id_user' => Yii::$app->user->id]);

        if($model != null) return $model;

        $model = new Forecasts();
        $model->id_game = $idGame;
        $model->id_user = Yii::$app->user->id;

        return $model;
    }
}
