<?php

namespace app\models\teams;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\teams\Teams;

/**
 * TeamsSearch represents the model behind the search form about `app\models\teams\teams`.
 */
class TeamsSearch extends teams
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_team', 'country'], 'integer'],
            [['team_name', 'team_logo'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = teams::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_team' => $this->id_team,
            'country' => $this->country,
        ]);

        $query->andFilterWhere(['like', 'team_name', $this->team_name])
            ->andFilterWhere(['like', 'team_logo', $this->team_logo]);

        return $dataProvider;
    }
}
