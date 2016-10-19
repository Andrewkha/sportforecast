<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/19/2016
 * Time: 11:15 AM
 */

namespace app\components\GamePointsCalculator;

use app\models\games\GamesExtended;

class playoffCalculator implements gamePointsCalculatorInterface
{
    const WIN = 10;
    const LOSE= 3;
    const DRAW = 5;

    public function setGamePoints(GamesExtended $game)
    {
        if($game->score_home === NULL || $game->score_guest === NULL)
        {
            $game->setHomePoints(NULL);
            $game->setGuestPoints(NULL);
            return;
        }

        if($game->score_home > $game->score_guest)
        {
            $game->setHomePoints(self::WIN);
            $game->setGuestPoints(self::LOSE);
            return;
        }

        if($game->score_home == $game->score_guest)
        {
            $game->setHomePoints(self::DRAW);
            $game->setGuestPoints(self::DRAW);
            return;
        }

        $game->setHomePoints(self::LOSE);
        $game->setGuestPoints(self::WIN);

        return;
    }

}