<?php
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 9/25/2015
 * Time: 4:31 PM
 */

namespace app\components\grid;

use kartik\grid\GridView;

class extendedGridView extends GridView
{
    public $export = false;
    public $responsive = false;
    public $responsiveWrap = false;

}