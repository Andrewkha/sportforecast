<?php
/**
 * Created by JetBrains PhpStorm.
 * User: achernys
 * Date: 4/23/15
 * Time: 6:14 PM
 * To change this template use File | Settings | File Templates.
 */
use yii\grid\GridView;
use yii\helpers\Html;

?>
<h4>Уважаемый <?= $user->username;?>!</h4>
<p>
    <?= $description;?>
</p>

</br>

<?= GridView::widget([
    'dataProvider' => $content,
    'summary' => '',
    'options' => [
        'style' => 'width: 50%'
    ],
    'filterUrl' => false,
    'rowOptions' => function($model) {

        return ($model['status'] == 1)? ['class' => 'success'] : ['class' => 'danger'];
    },
    'columns' => [

        [
            'attribute' => 'home_team',
            'header' => 'Хозяева',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-3',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
        ],

        [
            'attribute' => 'home_score_forecast',
            'header' => '',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-1',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
        ],

        [
            'attribute' => 'guest_score_forecast',
            'header' => '',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-1',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
        ],

        [
            'attribute' => 'guest_team',
            'header' => 'Гости',
            'headerOptions' => [
                'style' => 'text-align:center',
            ],
            'options' => [
                'class' => 'col-sm-3',
            ],
            'contentOptions' => [
                'align' => 'center',
                'style' => 'vertical-align:middle',
            ],
        ],
    ]
]);
?>
<br>
<?= Html::a('Сделать прогноз', Yii::$app->params['url']);?>