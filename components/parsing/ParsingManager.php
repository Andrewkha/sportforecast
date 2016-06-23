<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 6/23/2016
 * Time: 12:12 PM
 */

namespace app\components\parsing;

use app\models\tournaments\Tournaments;

class ParsingManager
{
    const EURO = 16;

    /**
     * @param $tournament Tournaments
     * @return euro2016Parsing|StandardParsing
     */

    public static function getParser($tournament)
    {
        switch ($tournament->id_tournament)
        {
            case self::EURO:
                return new euro2016Parsing($tournament);
                break;

            default:
                return new StandardParsing($tournament);
        }
    }
}