<?php

namespace app\models\tournaments;


use app\models\games\Games;
use app\models\news\News;
use app\models\users\UsersTournaments;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use simple_html_dom;
use Yii;
use app\models\traits\tournamentsTrait;

/**
 * This is the model class for table "{{%tournaments}}".
 *
 * @property integer $id_tournament
 * @property string $tournament_name
 * @property integer $country
 * @property integer $num_tours
 * @property integer $startsOn
 * @property string $is_active
 * @property string $autoProcessURL
 * @property integer $enableAutoprocess
 *
 * @property TeamTournaments[] $teamTournaments
 * @property Countries $country0
 * @property UsersTournaments[] $usersTournaments
 */
class Tournaments extends \yii\db\ActiveRecord
{
    use tournamentsTrait;

    const NOT_STARTED = '0';
    const GOING = '1';
    const FINISHED = '2';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tournaments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tournament_name', 'country', 'num_tours', 'is_active'], 'required'],
            [['country', 'num_tours', 'enableAutoprocess'], 'integer'],
            [['tournament_name',], 'string', 'max' => 255],
            ['autoProcessURL', 'url'],
            ['startsOn', 'date', 'format' => 'php:d.m.Y', 'timestampAttribute' => 'startsOn'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_tournament' => Yii::t('app', 'ID'),
            'tournament_name' => Yii::t('app', 'Название'),
            'country' => Yii::t('app', 'Страна'),
            'num_tours' => Yii::t('app', 'Количество туров'),
            'is_active' => Yii::t('app', 'Статус'),
            'startsOn' => 'Начало турнира',
            'enableAutoprocess' => 'Автозагрузка календаря',
            'autoProcessURL' => 'Источник данных',
        ];
    }

    public function afterSave($insert, $changedAttributes) {

        parent::afterSave($insert, $changedAttributes);

        if(isset($changedAttributes['is_active']) && $changedAttributes['is_active'] != self::FINISHED && $this->is_active == self::FINISHED) {

            News::updateAll(['archive' => News::ARCHIVE_TRUE], ['id_tournament' => $this->id_tournament] );
        }

        if($insert) {

            $news = new News();
            $news->scenario = 'send';
            $news->id_tournament = 0;
            $news->subject = 'Добавлен новый турнир';
            $news->body = "<p>Для прогноза доступен новый турнир {$this->tournament_name}, первый тур которого состоится ".date('d.m.y', $this->startsOn)." </p>"
                ."<p>Спешите принять участие! Зайдите в <strong>Профиль->Мои турниры</strong> чтобы начать делать прогнозы<p>";

            $news->save();

        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTeamTournaments()
    {
        return $this->hasMany(TeamTournaments::className(), ['id_tournament' => 'id_tournament']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry0()
    {
        return $this->hasOne(\app\models\countries\Countries::className(), ['id' => 'country']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsersTournaments()
    {
        return $this->hasMany(UsersTournaments::className(), ['id_tournament' => 'id_tournament']);
    }

    public function getStatus() {

        return self::statuses()[$this->is_active];
    }

    public static function statuses() {

        return [
            self::NOT_STARTED => 'Не начался',
            self::GOING => 'Проходит',
            self::FINISHED => 'Закончен',
        ];
    }

    public function autoProcess() {

        $teamTournament = TeamTournaments::find()
            ->where(['id_tournament' => $this->id_tournament])
            ->all();

        //getting array of team aliases and participant id's
        $aliases = ArrayHelper::map($teamTournament, 'alias', 'id');

        //getting Participant IDs for the tournament

        $teamIDs = ArrayHelper::getColumn($teamTournament, 'id');

        $url = $this->autoProcessURL;
        //$url = 'web/pl.html';

        $html = new simple_html_dom();

        $html->load_file($url);
        $count = $this->num_tours;
        $j = 0;

        $gamesFromWeb = [];
        for($i = 0; $i < $count; $i++) {

            $tour = $html->find('h3.titleH3', $i)->plaintext;

            $resultTable = $html->find('table.stat-table', $i);

            foreach($resultTable->find('tbody tr') as $k => $one) {

                 if($k != 0 && $this->autoTimeToUnix($one->find('td.name-td')[0]->plaintext) > time() - 60*60*24*7) {

                    if(isset($aliases[$one->find('td.owner-td a.player')[0]->plaintext]) && isset($aliases[$one->find('td.guests-td a.player')[0]->plaintext])) {

                        $gamesFromWeb[$j]['id_team_home'] = (int)$aliases[$one->find('td.owner-td a.player')[0]->plaintext];
                        $gamesFromWeb[$j]['id_team_guest'] = (int)$aliases[$one->find('td.guests-td a.player')[0]->plaintext];
                        $gamesFromWeb[$j]['date_time_game'] = (int)$this->autoTimeToUnix($one->find('td.name-td')[0]->plaintext);
                        $gamesFromWeb[$j]['tour'] = (int)trim($tour, "-ый тур");
                        $gamesFromWeb[$j]['score_home'] = (int)(trim(stristr($one->find('td.score-td noindex')[0]->plaintext, ':', true)) == '-') ? NULL : trim(stristr($one->find('td.score-td noindex')[0]->plaintext, ':', true));
                        $gamesFromWeb[$j]['score_guest'] = (int)(trim(trim(stristr($one->find('td.score-td noindex')[0]->plaintext, ':'), "\t\n\r\0\x0B\x3A")) == '-') ? NULL : trim(trim(stristr($one->find('td.score-td noindex')[0]->plaintext, ':'), "\t\n\r\0\x0B\x3A"));
                        $j++;
                    } else {
                        throw new Exception('Error during alias parsing '.$one->find('td.owner-td a.player')[0]->plaintext.' or '.$one->find('td.guests-td a.player')[0]->plaintext);
                    }
                }
            }
        }

        //all future games and previous where score is null
        $gamesFromDB = Games::find()
            ->where(['or',['in', 'id_team_home', $teamIDs], ['in', 'id_team_guest', $teamIDs]])
            ->andWhere(['or',
                ['>', 'date_time_game', time()],
                ['and',
                    ['<', 'date_time_game', time()],
                    ['or', ['score_home' => null],['score_guest' => null]]
                ],
            ])
            ->all();

        /*matching web data with DB data. for those that matches found:
            - if no changes, unset the game
            - if there're changes - updating the model and putting it to array for save
        */

        foreach($gamesFromDB as $gameDB) {
            foreach($gamesFromWeb as $k => $gameWeb) {

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

                    $unset = ArrayHelper::remove($gamesFromWeb, $k);
                    continue;
                }
            }
        }

        foreach($gamesFromWeb as $gameWeb) {

            $newGame = new Games();

            foreach($gameWeb as $attribute) {

                $newGame->$attribute = $gameWeb[$attribute];
            }
            $newGame->save(false);
        }

        return true;
    }

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
