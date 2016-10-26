<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/26/2016
 * Time: 12:52 PM
 */

namespace app\components\parsing;


class ChampionatStandardParsing extends championatParsing
{
    protected function calculateHomeScore($homeScore)
    {
        return (trim($homeScore) === '-')? NULL : (int)trim($homeScore);
    }

    protected function calculateGuestScore($guestScore)
    {
        return (trim($guestScore) === '-')? NULL : (int)trim($guestScore);
    }
}