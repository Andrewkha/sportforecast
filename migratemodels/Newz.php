<?php

namespace app\migratemodels;

use Yii;

/**
 * This is the model class for table "{{%newz}}".
 *
 * @property integer $id
 * @property string $subject
 * @property string $body
 * @property integer $user_id
 * @property integer $tournament_id
 * @property integer $date
 * @property integer $status
 *
 * @property User $user
 */
class Newz extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%newz}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subject', 'body'], 'required'],
            [['body'], 'string'],
            [['user_id', 'tournament_id', 'date', 'status'], 'integer'],
            [['subject'], 'string', 'max' => 1024],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'subject' => 'Subject',
            'body' => 'Body',
            'user_id' => 'User ID',
            'tournament_id' => 'Tournament ID',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
