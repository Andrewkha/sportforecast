<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 8/9/2016
 * Time: 4:58 PM
 */

namespace app\components\widgets;

use Yii;
use yii\base\Widget;

class Standings extends Widget
{
    public $standings;

    public function run()
    {
        return $this->render('standings', ['teamParticipants' => $this->standings]);
    }
}