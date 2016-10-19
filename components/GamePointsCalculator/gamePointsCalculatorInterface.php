<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/19/2016
 * Time: 10:55 AM
 */

namespace app\components\GamePointsCalculator;

use app\models\games\GamesExtended;

interface gamePointsCalculatorInterface
{
    public function setGamePoints(GamesExtended $game);
}