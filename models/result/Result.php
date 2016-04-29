<?php

namespace app\models\Result;

use Yii;


/**
 * This is the model class for table "{{%result}}".
 *
 * @property integer $id_game
 * @property integer $home_id
 * @property integer $guest_id
 * @property string $home_team
 * @property string $guest_team
 * @property string $home_logo
 * @property string $guest_logo
 * @property string $tournament_name
 * @property integer $id_tournament
 * @property string $tournament_country
 * @property string $is_active
 * @property string $home_participant_id
 * @property string $guest_participant_id
 * @property integer $home_score
 * @property integer $guest_score
 * @property integer $points_home
 * @property integer $points_guest
 * @property integer $tour
 * @property string $dtime
 */
class Result extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%result}}';
    }

    /**
     * @inheritdoc
     */


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_game' => Yii::t('app', 'ID игры'),
            'home_id' => Yii::t('app', 'ID хозяев'),
            'guest_id' => Yii::t('app', 'ID гостей'),
            'home_team' => Yii::t('app', 'Хозяева'),
            'guest_team' => Yii::t('app', 'Гости'),
            'home_logo' => Yii::t('app', 'Логотип'),
            'guest_logo' => Yii::t('app', 'Логотип'),
            'tournament_name' => Yii::t('app', 'Турнир'),
            'id_tournament' => Yii::t('app', 'ID турнира'),
            'tournament_country' => Yii::t('app', 'Страна'),
            'is_active' => Yii::t('app', 'Статус'),
            'home_participant_id' => Yii::t('app', 'Home Participant ID'),
            'guest_participant_id' => Yii::t('app', 'Guest Participant ID'),
            'home_score' => Yii::t('app', 'Счет хозяева'),
            'guest_score' => Yii::t('app', 'Счет гости'),
            'points_home' => Yii::t('app', 'Очки хозяева'),
            'points_guest' => Yii::t('app', 'Очки гости'),
            'tour' => Yii::t('app', 'Тур'),
            'dtime' => Yii::t('app', 'Начало матча'),
        ];
    }

    //last tour having games in tournament

    public static function getLastTour($tournament) {

        return self::find()->where(['id_tournament' => $tournament])->max('tour');
    }

    public static function getStandings($id) {

        $db = Yii::$app->db;
        $query = "SELECT res.id as participant, res.id as id, res.id_team, sf_teams.team_logo, sf_teams.team_name, res.id_tournament, pts, games_played  from sf_team_tournaments as res

                    left Join

                    (SELECT id_team, team, id_tournament, sum(points) as pts, count(team) as games_played, team_logo, team_name from

                    (SELECT home_id as id_team, home_team as team, id_tournament, points_home as points, home_logo as team_logo, home_team as team_name FROM sf_result

                        where (points_home is not null and id_tournament = $id)
                    UNION ALL
                        SELECT guest_id, guest_team, id_tournament, points_guest as points, guest_logo as team_logo, guest_team as team_name from sf_result
                        where (points_guest is not null and id_tournament = $id)) trn

                    group BY team
                    ) tt

                    on res.id_team = tt.id_team

                    join sf_teams on res.id_team = sf_teams.id_team
                    where res.id_tournament = $id
                    order by pts DESC";

        $standings = $db->createCommand($query)->queryAll();
        return $standings;
    }

    public static function getWinners($id)
    {
        $db = Yii::$app->db;
        $query = "SELECT res.id as participant, res.id as id, res.id_team, sf_teams.team_logo, sf_teams.team_name, res.id_tournament, pts, games_played  from sf_team_tournaments as res

                    left Join

                    (SELECT id_team, team, id_tournament, sum(points) as pts, count(team) as games_played, team_logo, team_name from

                    (SELECT home_id as id_team, home_team as team, id_tournament, points_home as points, home_logo as team_logo, home_team as team_name FROM sf_result

                        where (points_home is not null and id_tournament = $id)
                    UNION ALL
                        SELECT guest_id, guest_team, id_tournament, points_guest as points, guest_logo as team_logo, guest_team as team_name from sf_result
                        where (points_guest is not null and id_tournament = $id)) trn

                    group BY team
                    ) tt

                    on res.id_team = tt.id_team

                    join sf_teams on res.id_team = sf_teams.id_team
                    where res.id_tournament = $id
                    order by pts DESC 
                    limit 3";

        $standings = $db->createCommand($query)->queryAll();
        return $standings;
    }

    public static function getGamesTourTournament($tour, $tournament) {

        return
            self::find()
                ->where(['and', ['tour' => $tour], ['id_tournament' => $tournament]])
                ->all();
    }

    public static function getParticipantGames($id) {

        return self::find()
            ->where(['home_participant_id' => $id])
            ->orWhere(['guest_participant_id' => $id])
            ->all();
    }

    //get 5 last games between teams (use real team ids)
    public static function getLastFiveConfrontations($team1, $team2) {

        return
            self::find()
                ->where(['and', ['home_id' => $team1], ['guest_id' => $team2]])
                ->orWhere(['and', ['home_id' => $team2], ['guest_id' => $team1]])
                ->andWhere(['<', 'dtime', time()])
                ->orderBy(['dtime' => SORT_DESC])
                ->limit(5)
                ->all();
    }

    //get 5 last games for the team
    public static function getLastFiveGames($team) {

        return
            self::find()
                ->where(['or', ['home_id' => $team], ['guest_id' => $team]])
                ->andWhere(['<', 'dtime', time()])
                ->orderBy(['dtime' => SORT_DESC])
                ->limit(5)
                ->all();
    }

}
