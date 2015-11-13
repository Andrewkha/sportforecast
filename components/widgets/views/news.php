<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
/**
 * Created by PhpStorm.
 * User: achernys
 * Date: 11/12/2015
 * Time: 5:06 PM
 */

/**
 * @var $news \app\models\news\News
 */
?>
<p class = "text-center" style="font-size: 1.5em; color: #777;"><?= $title;?></p>
<?php foreach($news as $one):?>
    <div class = "row news text-left">
        <div class="col-xs-12">
            <strong><?= ($one->id_tournament == 0) ? 'Новости сайта' : $one->tournament->tournament_name;?></strong>
        </div>
        <div class="col-xs-12">
            <span class = "time"><?= date('d.m.Y H:i', $one->date);?></span>
            <?php Modal::begin([
                'header' => "<h4>".$one->subject."</h4>",
                'headerOptions' => [
                    'class' => 'bg-info'
                ],
                'toggleButton' => [
                    'tag' => 'a',
                    'label' => $one->subject,
                    'style' => 'font-size: 1em; cursor: pointer;',
                ],
                'footer' =>
                    "<div class='row'>".
                    "<div class='col-xs-7'>".
                    "<p class = 'pull-left'>Разместил: <strong>{$one->author0->username}</strong>"."</p>".
                    "</div>".
                    "<div class='col-xs-4'>".
                    "<p class='pull-right'>".date('d.m.Y H:i', $one->date)."</p>".
                    "</div>".
                    "</div>",
            ]);?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <?= $one->body;?>
                </div>
            </div>
            <?php Modal::end();?>
        </div>
    </div>
<?php endforeach;?>
<p class = 'text-right'>
    <?= Html::a("<i class = 'fa fa-newspaper-o'></i> Все новости", ['news/index']);?>
</p>
