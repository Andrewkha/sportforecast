<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/19/2016
 * Time: 10:57 AM
 */

namespace app\models\games;

use app\models\result\Result;
use app\components\GamePointsCalculator\Calculator;

class GamesExtended extends Games
{
    private $homePoints = null;
    private $guestPoints = null;

    public function afterFind()
    {
        parent::afterFind();
        $calculator = new Calculator($this);
        $calculator->setGamePoint();
    }

    /**
     * @param null $homePoints
     */
    public function setHomePoints($homePoints)
    {
        $this->homePoints = $homePoints;
    }

    /**
     * @param null $guestPoints
     */
    public function setGuestPoints($guestPoints)
    {
        $this->guestPoints = $guestPoints;
    }

    /**
     * @return null
     */
    public function getHomePoints()
    {
        return $this->homePoints;
    }

    /**
     * @return null
     */
    public function getGuestPoints()
    {
        return $this->guestPoints;
    }

    public function getTournamentID()
    {
        return Result::findOne(['id_game' => $this->id_game])->id_tournament;
    }
}