<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 6/23/2016
 * Time: 12:11 PM
 */

namespace app\components\parsing;

use app\components\parsing\AParsing;

class euro2016Parsing extends AParsing
{
    const TOURS = [
        'Тур 1' => 1,
        'Тур 2' => 2,
        'Тур 3' => 3,
        '1/8 финала' => 4,
        '1/4 финала' => 5,
        'Полуфинал' => 6,
        'Финал' => 7
    ];

    public function getTour($tour)
    {
        return self::TOURS[$tour];
    }

    public function getTourTitle()
    {
        // TODO: Implement getTourTitle() method.
    }

    protected function calculateHomeScore($score)
    {
        if(mb_substr($score, 0, 1) == 'п')
            return (int)(trim(stristr($score, ':', true)) == '-') ? NULL : (int)trim(stristr($score, ':', true)) + 1;
        else
            return (int)(trim(stristr($score, ':', true)) == '-') ? NULL : trim(stristr($score, ':', true));
    }

    protected function calculateGuestScore($score)
    {
        if(mb_substr($score, -1 ,1) == 'п')
            return (int)(trim(trim(stristr($score, ':'), "\t\n\r\0\x0B\x3A")) == '-') ? NULL : (int)trim(trim(stristr($score, ':'), "\t\n\r\0\x0B\x3A")) + 1;
        else
            return (int)(trim(trim(stristr($score, ':'), "\t\n\r\0\x0B\x3A")) == '-') ? NULL : trim(trim(stristr($score, ':'), "\t\n\r\0\x0B\x3A"));
    }
}