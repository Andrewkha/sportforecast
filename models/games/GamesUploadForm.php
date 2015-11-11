<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 3/20/15
 * Time: 3:30 PM
 * To change this template use File | Settings | File Templates.
 */

namespace app\models\games;

use yii\base\Model;
use Yii;

class GamesUploadForm extends Model{

    public $file;

    public function rules() {

        return [
            [['file'], 'required'],
            ['file', 'file', 'extensions' => ['xls', 'xlsx'], 'mimeTypes' => ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel.12'], 'maxSize' => 1024*1024],
        ];
    }

}