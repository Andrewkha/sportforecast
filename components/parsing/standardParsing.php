<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 6/23/2016
 * Time: 11:55 AM
 */

namespace app\components\parsing;

class StandardParsing extends sportsParsing
{

    public function getTour($tour)
    {
        return preg_replace("/[^0-9]/", '', $tour);
    }

    public function getTourTitle()
    {
        // TODO: Implement getTourTitle() method.
    }

    protected function calculateHomeScore($score)
    {
        return (int)(trim(stristr($score, ':', true)) == '-') ? NULL : trim(stristr($score, ':', true));
    }
    
    protected function calculateGuestScore($score)
    {
        return (int)(trim(trim(stristr($score, ':'), "\t\n\r\0\x0B\x3A")) == '-') ? NULL : trim(trim(stristr($score, ':'), "\t\n\r\0\x0B\x3A"));
    }
}