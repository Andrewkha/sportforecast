<?php

namespace app\models\reminders;

use Yii;
use yii\behaviors\TimestampBehavior;
use app\models\tournaments\Tournaments;
use app\models\traits\remindersTrait;

/**
 * This is the model class for table "{{%reminders_user_tournament_tour}}".
 *
 * @property integer $id
 * @property string $user
 * @property integer $tournament
 * @property integer $tour
 * @property integer $reminders
 * @property integer $date
 *
 * @property Tournaments $tournament0
 * @property Users $user0
 */
class Reminders extends \yii\db\ActiveRecord
{

    use remindersTrait;

    const NUM_REMINDERS = 2;

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
    public static function tableName()
    {
        return '{{%reminders_user_tournament_tour}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user', 'tournament', 'tour', 'reminders', 'date'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user' => 'User',
            'tournament' => 'Tournament',
            'tour' => 'Tour',
            'reminders' => 'Reminders',
            'date' => 'Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament0()
    {
        return $this->hasOne(Tournaments::className(), ['id_tournament' => 'tournament']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser0()
    {
        return $this->hasOne(Users::className(), ['id' => 'user']);
    }

    public function beforeSave($insert) {

        if(parent::beforeSave($insert)) {

            if($insert) {
               $this->reminders = 1;
            } else {
               $this->reminders++;
            }

            return true;
        } else {
            return false;
        }
    }


    //if a user eligible for autoreminder (no more than 2 reminders)
    public static function ifEligible($tournament, $tour, $user) {

        $search = self::find()
            ->where(['tournament' => $tournament])
            ->andWhere(['tour' => $tour])
            ->andWhere(['user' => $user]);

        if(!$search->exists())
            return true;

        if($search->one()->reminders < self::NUM_REMINDERS)
            return true;
        else
            return false;
    }
}
