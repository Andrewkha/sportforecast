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

    abstract protected function getGamesFromWeb($teamTournament);


    protected function getGamesFromDB($teamTournament)
    {
        $teamIDs = ArrayHelper::getColumn($teamTournament, 'id');
        $this->gamesFromDB = Games::find()
            ->where(['or',['in', 'id_team_home', $teamIDs], ['in', 'id_team_guest', $teamIDs]])
            ->andWhere(['>', 'date_time_game', time() - 60*60*24*7*4])
            ->all();
    }


    protected function matchWebDB()
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

    protected function addNewGames()
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


}