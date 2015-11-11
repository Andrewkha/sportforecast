<?php

use kartik\grid\GridView;
use yii\helpers\StringHelper;
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 6/18/15
 * Time: 5:51 PM
 * To change this template use File | Settings | File Templates.
 */
?>
<div class = "row">
    <?= GridView::widget([
        'dataProvider' => $details['recentHome'],
        'export' => false,
        'options' => [
            'class' => 'col-xs-10 col-sm-6 col-lg-4',
        ],
        'summary' => '',
        'responsive' => false,
        'showHeader' => false,
        'caption' => "Последние матчи ".StringHelper::truncateWords($details['idTeamHome']['idTeam']['team_name'], 1, ''),
        'columns' => [

            [
                'content' => function ($model) {

                    return date('d.m.y', $model['dtime']).' '.StringHelper::truncateWords($model['home_team'], 1, '').' '.$model['home_score'].' - '.
                    $model['guest_score'].' '.StringHelper::truncateWords($model['guest_team'], 1, '');
                }
            ]
        ]
    ]);?>

    <?= GridView::widget([
        'dataProvider' => $details['recent'],
        'export' => false,
        'options' => [
            'class' => 'col-xs-10 col-sm-6 col-lg-4',
        ],
        'summary' => '',
        'responsive' => false,
        'showHeader' => false,
        'caption' => "Последние очные встречи",
        'columns' => [

            [
                'content' => function ($model) {

                    return date('d.m.y', $model['dtime']).' '.StringHelper::truncateWords($model['home_team'], 1, '').' '.$model['home_score'].' - '.
                    $model['guest_score'].' '.StringHelper::truncateWords($model['guest_team'], 1, '');
                }
            ]
        ]
    ]);?>

    <?= GridView::widget([
        'dataProvider' => $details['recentGuest'],
        'export' => false,
        'options' => [
            'class' => 'col-xs-10 col-sm-6 col-lg-4',
        ],
        'summary' => '',
        'responsive' => false,
        'showHeader' => false,
        'caption' => "Последние матчи ".StringHelper::truncateWords($details['idTeamGuest']['idTeam']['team_name'], 1, ''),
        'columns' => [

            [
                'content' => function ($model) {

                    return date('d.m.y', $model['dtime']).' '.StringHelper::truncateWords($model['home_team'], 1, '').' '.$model['home_score'].' - '.
                    $model['guest_score'].' '.StringHelper::truncateWords($model['guest_team'], 1, '');
                }
            ]
        ]
    ]);?>
</div>