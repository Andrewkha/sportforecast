<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 11/23/2016
 * Time: 3:29 PM
 */

namespace app\Service;


use app\components\GamePointsCalculator\Calculator;

class GameService
{
    private $calculator;

    public function __construct(Calculator $calculator)
    {
        $this->calculator = $calculator;
    }
}