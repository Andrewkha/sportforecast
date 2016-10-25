<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 10/25/2016
 * Time: 4:52 PM
 */

namespace app\components\parsing;

use DiDom\Document;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

abstract class sportsParsing extends AParsing
{
    protected function getGamesFromWeb($teamTournament)
    {
        //getting array of team aliases and participant id's
        $aliases = ArrayHelper::map($teamTournament, 'alias', 'id');

        $count = $this->tournament->num_tours;
        $j = 0;

        $html = new Document($this->tournament->autoProcessURL, true);
        //$html = new Document('pl.htm', true);
        $results = $html->find('div.mainPart')[0];
        $gamesFromWeb = [];

        for($i = 0; $i < $count; $i++) {

            if(isset($results->find('div.stat.mB15 table.stat-table')[$i]))
            {
                $tour = $html->find('h3.titleH3.bordered.mB10')[$i]->text();
                $tour = $this->getTour($tour);

                $resultTable = $results->find('div.stat.mB15 table.stat-table')[$i];
                foreach($resultTable->find('tbody tr') as $k => $one) {
                    if($this->autoTimeToUnix($one->find('td.name-td')[0]->text()) > time() - 60*60*24*7*4 && $tour <= $count)
                    {
                        if(isset($one->find('td.owner-td a.player')[0]) && isset($one->find('td.guests-td a.player')[0]))
                        {
                            $owner = $one->find('td.owner-td a.player')[0]->text();
                            $guest = $one->find('td.guests-td a.player')[0]->text();
                            if(isset($aliases[$owner]) && isset($aliases[$guest])) {

                                $gamesFromWeb[$j]['id_team_home'] = (int)$aliases[$owner];
                                $gamesFromWeb[$j]['id_team_guest'] = (int)$aliases[$guest];
                                $gamesFromWeb[$j]['date_time_game'] = (int)$this->autoTimeToUnix($one->find('td.name-td')[0]->text());
                                $gamesFromWeb[$j]['tour'] = $tour;
                                $score = $one->find('td.score-td noindex')[0]->text();
                                $gamesFromWeb[$j]['score_home'] = $this->calculateHomeScore($score);
                                $gamesFromWeb[$j]['score_guest'] = $this->calculateGuestScore($score);
                                $j++;
                            } else {

                                throw new Exception('Error during alias parsing '.$owner.' or '.$guest);
                            }
                        }
                    }
                }
            }
        }

        $this->gamesFromWeb = $gamesFromWeb;
    }

    abstract protected function calculateHomeScore($score);

    abstract protected function calculateGuestScore($score);

    abstract public function getTour($tour);

    abstract public function getTourTitle();

    private function autoTimeToUnix($str) {

        $day = substr($str, 0, 2);
        $str = trim(substr($str, 3));

        $month = substr($str, 0, 2);
        $str = trim(substr($str, 3));

        $year = substr($str, 0, 4);

        $str = trim(substr($str, 5));
        $hour = substr($str, 0, 2);

        $str = trim(substr($str, 3));
        $min = substr($str, 0, 2);

        return mktime($hour, $min , 0, $month, $day, $year);
    }
}