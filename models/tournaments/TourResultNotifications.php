<?php

namespace app\models\tournaments;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "{{%tour_result_notification}}".
 *
 * @property integer $id
 * @property integer $tournament
 * @property integer $tour
 * @property integer $date
 */
class TourResultNotifications extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tour_result_notification}}';
    }

    public function behaviors() {

        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'date',
                'updatedAtAttribute' => 'date',
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tournament', 'tour', 'date'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tournament' => 'Турнир',
            'tour' => 'Тур',
            'date' => 'Дата',
        ];
    }

    public static function ifExists($tour, $tournament) {

        return self::find()
            ->where(['and', ['tour' => $tour], ['tournament' => $tournament]])
            ->exists();
    }
}
