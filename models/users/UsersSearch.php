<?php

namespace app\models\users;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\users\Users;

/**
 * UsersSearch represents the model behind the search form about `app\models\users\users`.
 */
class UsersSearch extends users
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_on', 'last_login', 'active', 'notifications'], 'integer'],
            [['username', 'password', 'email', 'forgotten_password_code', 'first_name', 'last_name', 'avatar', 'auth_key'], 'safe'],
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
        $query = users::find();

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
            'id' => $this->id,
            'created_on' => $this->created_on,
            'last_login' => $this->last_login,
            'active' => $this->active,
            'notifications' => $this->notifications,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'forgotten_password_code', $this->forgotten_password_code])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'avatar', $this->avatar])
            ->andFilterWhere(['like', 'auth_key', $this->auth_key]);

        return $dataProvider;
    }
}
