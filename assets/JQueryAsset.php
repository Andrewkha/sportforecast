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
class JQueryAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
    public $css = [
        '//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css'
    ];
    public $js = [
        '//code.jquery.com/jquery-1.9.1.js',
        '//code.jquery.com/ui/1.10.4/jquery-ui.js'
    ];
    public $depends = [
        'app\assets\AppAsset',
    ];
}
