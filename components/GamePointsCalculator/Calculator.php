<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/19/2016
 * Time: 11:17 AM
 */

namespace app\components\GamePointsCalculator;

use app\models\games\GamesExtended;

class Calculator
{
    private $game;
    private $calculator;

    public function __construct(GamesExtended $game)
    {
        $this->game = $game;
        $this->calculator = $this->getCalculator();
    }

    private function getCalculator() : gamePointsCalculatorInterface
    {
        if($this->game->getTournamentID() == 17)
            return new standardCalculator();
        else
            return new playoffCalculator();
    }

    public function setGamePoint()
    {
        $this->calculator->setGamePoints($this->game);
    }
}