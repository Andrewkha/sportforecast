<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 4/26/2016
 * Time: 10:27 AM
 */

namespace app\models\forecasts;

use app\models\forecasts\Top3TeamsForecast;
use yii\base\Model;


class TopTeamsForm extends Model
{
    public $first;
    public $second;
    public $third;

    private $_models = [];

    public function __construct($user, $tournament, array $config = [])
    {
        $this->getForecast($user,$tournament);
        $this->first = $this->_models[1]->id_participant_team;
        $this->second = $this->_models[2]->id_participant_team;
        $this->third = $this->_models[3]->id_participant_team;

        parent::__construct($config);
    }

    public function rules()
    {
        return [
            ['first', 'compare', 'compareAttribute' => 'second', 'operator' => '!=', 'message' => 'Команды должны быть разные'],
            ['first', 'compare', 'compareAttribute' => 'third', 'operator' => '!=', 'message' => 'Команды должны быть разные'],
            ['second', 'compare', 'compareAttribute' => 'third', 'operator' => '!=', 'message' => 'Команды должны быть разные'],
            ['second', 'compare', 'compareAttribute' => 'first', 'operator' => '!=', 'message' => 'Команды должны быть разные'],
            ['third', 'compare', 'compareAttribute' => 'first', 'operator' => '!=', 'message' => 'Команды должны быть разные'],
            ['third', 'compare', 'compareAttribute' => 'second', 'operator' => '!=', 'message' => 'Команды должны быть разные'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'first' => 'Первое место',
            'second' => 'Второе место',
            'third' => 'Третье место',
        ];
    }

    private function getForecast($user, $tournament)
    {
        for($i = 1; $i <= 3; $i++)
        {
            if(Top3TeamsForecast::find()->findModel($user, $tournament, $i)->exists())
            {
                $this->_models[$i] = Top3TeamsForecast::find()->findModel($user, $tournament, $i)->one();
            } else
            {
                $this->_models[$i] = new Top3TeamsForecast();
                $this->_models[$i]->id_user = $user;
                $this->_models[$i]->id_tournament = $tournament;
                $this->_models[$i]->forecasted_position = $i;
            }
        }

        return $this->_models;
    }

    public function edit() {

        $models = $this->_models;

        $models[1]->id_participant_team = $this->first;
        $models[2]->id_participant_team = $this->second;
        $models[3]->id_participant_team = $this->third;

        for($i = 1; $i <= 3; $i++)
        {
            if($models[$i]->validate())
                $models[$i]->save();
            else {
                var_dump($models[$i]->getOldAttribute('id_participant_team') != NULL);
                if($models[$i]->getOldAttribute('id_participant_team') != NULL && $models[$i]->id_participant_team == '')
                    $models[$i]->delete();
            }
        }
    }
}