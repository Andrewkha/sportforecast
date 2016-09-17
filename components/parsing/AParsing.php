<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 6/23/2016
 * Time: 11:51 AM
 */

namespace app\components\parsing;


use app\models\forecasts\Forecasts;
use app\models\tournaments\Tournaments;
use DiDom\Document;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use app\models\tournaments\TeamTournaments;
use app\models\games\Games;

abstract class AParsing
{
    /**

     * @property Games[] $gamesFromWeb
     * @property Games[] $gamesFromDBs
     */

    protected $tournament;
    protected $gamesFromWeb;
    protected $gamesFromDB;

    /**
     * AParsing constructor.
     * @param $tournament Tournaments
     */

    public function __construct($tournament)
    {
        $this->tournament = $tournament;
    }

    private function getGamesFromWeb($teamTournament)
    {
        //getting array of team aliases and participant id's
        $aliases = ArrayHelper::map($teamTournament, 'alias', 'id');

        $count = $this->tournament->num_tours;
        $j = 0;

        $html = new Document($this->tournament->autoProcessURL, true);
        //$html = new Document('pl.htm', true);
        $gamesFromWeb = [];

        for($i = 0; $i < $count; $i++) {

            if(isset($html->find('h3.titleH3.bordered.mB10')[$i]))
            {
                $tour = $html->find('h3.titleH3.bordered.mB10')[$i]->text();
                $tour = $this->getTour($tour);
                $resultTable = $html->find('table.stat-table')[$i];
                foreach($resultTable->find('tbody tr') as $k => $one) {

                    if($this->autoTimeToUnix($one->find('td.name-td')[0]->text()) > time() - 60*60*24*7*2 && $tour <= $count)
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

    private function getGamesFromDB($teamTournament)
    {
        $teamIDs = ArrayHelper::getColumn($teamTournament, 'id');
        $this->gamesFromDB = Games::find()
            ->where(['or',['in', 'id_team_home', $teamIDs], ['in', 'id_team_guest', $teamIDs]])
            ->andWhere(['>', 'date_time_game', time() - 60*60*24*7*2])
            ->all();
    }


    private function matchWebDB()
    {
        foreach($this->gamesFromDB as $gameDB) {
            foreach($this->gamesFromWeb as $k => $gameWeb) {

                if($gameWeb['tour'] == $gameDB->tour && $gameWeb['id_team_home'] == $gameDB->id_team_home && $gameWeb['id_team_guest'] == $gameDB->id_team_guest) {

                    if($gameDB->date_time_game != $gameWeb['date_time_game']) {
                        $gameDB->date_time_game = $gameWeb['date_time_game'];
                    }

                    if($gameDB->score_home != $gameWeb['score_home']) {
                        $gameDB->score_home = $gameWeb['score_home'];
                    }

                    if($gameDB->score_guest != $gameWeb['score_guest']) {
                        $gameDB->score_guest = $gameWeb['score_guest'];
                    }

                    $dirtyAttr = $gameDB->getDirtyAttributes();

                    if(!empty($dirtyAttr)) {

                        $gameDB->save(false);
                    }

                    $unset = ArrayHelper::remove($this->gamesFromWeb, $k);
                    continue;
                }

                //if home and guest switched
                if($gameWeb['tour'] == $gameDB->tour && $gameWeb['id_team_home'] == $gameDB->id_team_guest && $gameWeb['id_team_guest'] == $gameDB->id_team_home)
                {

                    $gameDB->id_team_home = $gameWeb['id_team_home'];
                    $gameDB->id_team_guest = $gameWeb['id_team_guest'];

                    if($gameDB->date_time_game != $gameWeb['date_time_game']) {
                        $gameDB->date_time_game = $gameWeb['date_time_game'];
                    }

                    //need to switch forecast home <=> guest
                    $forecasts = Forecasts::find()
                        ->where(['id_game' => $gameDB->id_game])
                        ->all();

                    foreach ($forecasts as $forecast) {

                        $temp = $forecast->fscore_home;
                        $forecast->fscore_home = $forecast->fscore_guest;
                        $forecast->fscore_guest = $temp;
                        $forecast->save(false);
                    }

                    $gameDB->save(false);

                    $unset = ArrayHelper::remove($this->gamesFromWeb, $k);
                    continue;
                }
            }
        }
    }

    private function addNewGames()
    {
        foreach($this->gamesFromWeb as $gameWeb) {

            $newGame = new Games();

            foreach($gameWeb as $k => $attribute) {

                $newGame->$k = $attribute;
            }

            if(!Games::find()
                ->where(['tour' => $newGame->tour])
                ->andWhere(['id_team_home' => $newGame->id_team_home])
                ->andWhere(['id_team_guest' => $newGame->id_team_guest])
                ->exists()
            )
                $newGame->save(false);
        }
    }

    abstract protected function calculateHomeScore($score);

    abstract protected function calculateGuestScore($score);

    public function parse()
    {
        $teamTournament = TeamTournaments::find()
            ->where(['id_tournament' => $this->tournament->id_tournament])
            ->all();

        $this->getGamesFromWeb($teamTournament);
        $this->getGamesFromDB($teamTournament);
        $this->matchWebDB();
        $this->addNewGames();
    }

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