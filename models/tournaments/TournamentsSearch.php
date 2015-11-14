<?php

namespace app\models\tournaments;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\tournaments\Tournaments;
use yii\data\Sort;

/**
 * TournamentsSearch represents the model behind the search form about `app\models\tournaments\tournaments`.
 */
class TournamentsSearch extends Tournaments
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_tournament', 'country', 'num_tours'], 'integer'],
            [['tournament_name', 'is_active', 'startsOn'], 'safe'],
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
        $query = Tournaments::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => new Sort([
                'attributes' => [
                    'startsOn',
                    'country',
                    'is_active',
                    'tournament_name'
                ],
                'defaultOrder' => [
                    'startsOn' => SORT_DESC,
                ],
            ])
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id_tournament' => $this->id_tournament,
            'country' => $this->country,
            'num_tours' => $this->num_tours,
        ]);

        $query->andFilterWhere(['like', 'tournament_name', $this->tournament_name])
            ->andFilterWhere(['like', 'is_active', $this->is_active]);

        return $dataProvider;
    }

}
