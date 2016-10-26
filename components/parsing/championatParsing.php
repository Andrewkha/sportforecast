<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/25/2016
 * Time: 4:38 PM
 */

namespace app\components\parsing;

use yii\helpers\ArrayHelper;
use DiDom\Document;

abstract class championatParsing extends AParsing
{
    protected function getGamesFromWeb($teamTournament)
    {
        //getting array of team aliases and participant id's
        $aliases = ArrayHelper::map($teamTournament, 'alias', 'id');

        $count = $this->tournament->num_tours;

        //$html = new Document($this->tournament->autoProcessURL, true);
        $html = new Document('pl_ch.htm', true);

        $table = $html->find('table.table.b-table-sortlist tbody')[0];

        $j = 0;
        $gamesFromWeb = [];

        foreach ($table->find('tr') as $row)
        {
            $time = $this->autoTimeToUnix($row->find('td.sport__calendar__table__date')[0]->text());

            if($time > time() - 60*60*24*7*2)
            {
                $home = $row->find('td.sport__calendar__table__teams a.sport__calendar__table__team')[0]->text();
                $guest = $row->find('td.sport__calendar__table__teams a.sport__calendar__table__team')[1]->text();

                if(isset($aliases[$home]) && isset($aliases[$guest])) {
                    $gamesFromWeb[$j]['tour'] = $row->find('td.sport__calendar__table__tour')[0]->text();
                    $gamesFromWeb[$j]['date_time_game'] = $this->autoTimeToUnix($row->find('td.sport__calendar__table__date')[0]->text());
                    $gamesFromWeb[$j]['id_team_home'] = (int)$aliases[$home];
                    $gamesFromWeb[$j]['id_team_guest'] = (int)$aliases[$guest];

                    $homeScore = $row->find('td.sport__calendar__table__result span.sport__calendar__table__result__left')[0]->text();
                    $guestScore = $row->find('td.sport__calendar__table__result span.sport__calendar__table__result__right')[0]->text();

                    $gamesFromWeb[$j]['score_home'] = $this->calculateHomeScore($homeScore);
                    $gamesFromWeb[$j]['score_guest'] = $this->calculateHomeScore($guestScore);

                    $j++;
                } else {
                    throw new \Exception('Error during alias parsing '.$home.' or '.$guest);
                }
            }
        }

        $this->gamesFromWeb = $gamesFromWeb;
    }

    abstract protected function calculateHomeScore($score);

    abstract protected function calculateGuestScore($score);

    private function autoTimeToUnix($str)
    {
        $str = trim($str);

        $day = (int)substr($str, 0, 2);
        $str = trim(substr($str, 3));

        $month = (int)substr($str, 0, 2);
        $str = trim(substr($str, 3));

        $year = (int)substr($str, 0, 4);

        $str = trim(substr($str, 5));
        $hour = (int)substr($str, 0, 2);

        $str = trim(substr($str, 3));
        $min = (int)substr($str, 0, 2);

        return mktime($hour, $min , 0, $month, $day, $year);
    }
}