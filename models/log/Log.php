<?php

namespace app\models\log;

use Yii;
use yii\log\Logger;

/**
 * This is the model class for table "{{%log}}".
 *
 * @property string $id
 * @property integer $level
 * @property string $category
 * @property double $log_time
 * @property string $prefix
 * @property string $message
 */
class Log extends \yii\db\ActiveRecord
{
    const CATEGORY_CONSOLE = 'console';
    const CATEGORY_APPLICATION = 'application';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level'], 'integer'],
            [['log_time'], 'number'],
            [['prefix', 'message'], 'string'],
            [['category'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Тип',
            'category' => 'Категроия',
            'log_time' => 'Дата',
            'prefix' => 'Префикс',
            'message' => 'Содержание',
        ];
    }

    public static function getStatusesArray() {

        return [
            Logger::LEVEL_ERROR => 'Ошибка',
            Logger::LEVEL_INFO => 'Информация',
            Logger::LEVEL_WARNING => 'Предупреждение',
            Logger::LEVEL_TRACE => 'Отладочная информация',
        ];
    }

    public static function getClassesArray() {

        return [
            Logger::LEVEL_ERROR => 'danger',
            Logger::LEVEL_INFO => 'info',
            Logger::LEVEL_WARNING => 'warning',
            Logger::LEVEL_TRACE => 'success',
        ];
    }

    public function getStatus() {

        return self::getStatusesArray()[$this->level];
    }

    public function getClass() {

        return self::getClassesArray()[$this->level];
    }
}
