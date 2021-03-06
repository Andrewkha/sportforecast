<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 4/19/2016
 * Time: 3:08 PM
 */

namespace app\models\query;

use yii\db\ActiveQuery;

class UsersTournamentsQuery extends ActiveQuery
{
    /**
     * @inheritdoc
     * @return \app\models\Product[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Product|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function forecastersStandings($id)
    {
        return $this->andWhere(['id_tournament' => $id])
            ->with('idUser')
            ->with('idTournament')
            ->orderBy(['points' => SORT_DESC]);
    }

    public function leader($id)
    {
        return $this->forecastersStandings($id)->limit(1);
    }

    public function userParticipates($user)
    {
        return $this->andWhere(['id_user' => $user]);
    }

    public function findModel($tournament, $user)
    {
        return $this->andWhere(['id_tournament' => $tournament])
        ->andWhere(['id_user' => $user]);
    }
}