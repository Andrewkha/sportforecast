<?php

namespace app\models\tournaments;

use app\models\teams\Teams;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use app\models\games\Games;

/**
 * This is the model class for table "{{%team_tournaments}}".
 *
 * @property string $id
 * @property integer $id_team
 * @property integer $id_tournament
 *
 * @property Games[] $games
 * @property Teams $idTeam
 * @property Tournaments $idTournament
 */
class TeamTournaments extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%team_tournaments}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_team', 'id_tournament'], 'required'],
            [['id_team', 'id_tournament'], 'integer'],
            ['alias', 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID команды в турнире'),
            'id_team' => Yii::t('app', 'Команда'),
            'id_tournament' => Yii::t('app', 'Id Tournament'),
            'alias' => 'Псевдоним автопроцессинга'
        ];
    }

    //add a participant for the tournament

    public function addParticipant($idCandidate, $tournament) {

        $this->id_team = $idCandidate;
        $this->id_tournament = $tournament;
        $this->save();
    }


    //get tournament participants IDs

    public static function getTournamentParticipantsID($id) {

        $candidates = self::getTournamentParticipants($id)->all();
        return ArrayHelper::getColumn($candidates, 'id_team');
    }
    /**
     * @param $id
     * @return \yii\db\QueryInterface
     */
    //get participants for the specified tournament
    public static function getTournamentParticipants($id) {

        return self::find()->where(['id_tournament' => $id]);
    }


    /**
     * get participants for the specified tournament
     * @param $id
     * @var $query \yii\db\QueryInterface
     * @return array|ActiveRecord[]
     */
    public static function getTournamentParticipantsTeams($id) {

        return self::find()
            ->where(['id_tournament' => $id])
            ->with(['idTeam' => function($query){$query->orderBy('team_name', 'asc');}])
            ->indexBy('id')
            ->all();
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGames()
    {
        return $this->hasMany(Games::className(), ['id_team_home' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTeam()
    {
        return $this->hasOne(Teams::className(), ['id_team' => 'id_team']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTournament()
    {
        return $this->hasOne(Tournaments::className(), ['id_tournament' => 'id_tournament']);
    }


}
