<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class TimePickerAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
    public $css = [
        'css/jquery-ui-timepicker-addon.css',
    ];
    public $js = [
        'js/jquery-ui-timepicker-addon.js',
    ];
    public $depends = [
        'app\assets\JQueryAsset'
    ];
}
