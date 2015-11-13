<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 11/12/2015
 * Time: 5:02 PM
 */

namespace app\components\widgets;

use yii\base\Widget;

class News extends Widget
{

    public $title;

    public function run() {

        $title = $this->title;
        $news = \app\models\news\News::getTopActiveNews();
        return $this->render('news', compact('news', 'title'));
    }
}