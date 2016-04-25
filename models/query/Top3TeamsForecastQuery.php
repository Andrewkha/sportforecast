<?php

namespace app\models\query;

/**
 * This is the ActiveQuery class for [[\app\models\forecasts\Top3TeamsForecast]].
 *
 * @see \app\models\forecasts\Top3TeamsForecast
 */
class Top3TeamsForecastQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return \app\models\forecasts\Top3TeamsForecast[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\forecasts\Top3TeamsForecast|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}