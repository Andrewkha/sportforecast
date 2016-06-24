<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 6/24/2016
 * Time: 12:24 PM
 */

namespace app\components\widgets;

use Yii;
use app\models\games\Games;
use yii\base\Widget;

class frontPageGames extends Widget
{
    private $_games;
    public $type;
    private $_view;

    public function init()
    {
        parent::init();

        if(!Yii::$app->user->isGuest)
            $user = Yii::$app->user;
        else
            $user = false;

        if($this->type == 'recent')
        {
            if($user)
            {
                $this->_games = Games::getAllRecentGamesWithForecast($user->id);
                $this->_view = 'recentGamesUser';
            }
            else
            {
                $this->_games = Games::getAllRecentGames();
                $this->_view = 'recentGamesGuest';
            }
        }
        else
        {
            if($user)
            {
                $this->_games = Games::getAllFutureGamesWithForecast($user->id);
                $this->_view = 'futureGamesUser';
            }
            else
            {
                $this->_games = Games::getAllFutureGames();
                $this->_view = 'futureGamesGuest';
            }
        }
    }

    public function run()
    {
        return $this->render($this->_view, ['games' => $this->_games]);
    }
}