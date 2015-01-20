<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\NotificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notifications';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
<?php // echo $this->render('_search', ['model' => $searchModel]);  ?>


    <?php
    $toolbars = [
        ['content' =>
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['notification/index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => 'Reset Grid']) . ' '
        ],
        ['content' => '{dynagridFilter}{dynagridSort}{dynagrid}'],
        '{export}',
    ];
    $panels = [
        'heading' => '<h3 class="panel-title"><i class="glyphicon glyphicon-book"></i>  ' . $this->title . '</h3>',
        'before' => '<div style="padding-top: 7px;"><em>* The table at the right you can pull reports & personalize</em></div>',
    ];
    $columns = [
        ['class' => 'kartik\grid\SerialColumn', 'order' => DynaGrid::ORDER_FIX_LEFT],
        'id',
        'title',
        'message:ntext',
        'url:url',
        'params:ntext',
        [
            'attribute' => 'userId',
            'format' => 'html',
            'filter' => User::dropdown(),
            'value' => function($data) {
                return $data->user->username;
            },
        ],
       
        [
            'attribute' => 'updateDate',
            'filterType' => GridView::FILTER_DATE,
            'format' => 'raw',
            'width' => '170px',
            'filterWidgetOptions' => [
                'pluginOptions' => ['format' => 'yyyy-mm-dd']
            ],
        ],
        [
            'attribute' => 'createDate',
            'filterType' => GridView::FILTER_DATE,
            'format' => 'raw',
            'width' => '170px',
            'filterWidgetOptions' => [
                'pluginOptions' => ['format' => 'yyyy-mm-dd']
            ],
        ],
//        [
//            'class' => 'kartik\grid\ActionColumn',
//            'dropdown' => false,
//            'vAlign' => 'middle',
//            'viewOptions' => ['title' => 'view', 'data-toggle' => 'tooltip'],
//            'updateOptions' => ['title' => 'update', 'data-toggle' => 'tooltip'],
//            'deleteOptions' => ['title' => 'delete', 'data-toggle' => 'tooltip'],
//        ],
        [
            'class' => 'kartik\grid\CheckboxColumn',
        ],
    ];

    $dynagrid = DynaGrid::begin([
                'id' => 'user-grid',
                'columns' => $columns,
                'theme' => 'panel-primary',
                'showPersonalize' => true,
                'storage' => 'db',
                //'maxPageSize' =>500,
                'allowSortSetting' => true,
                'gridOptions' => [
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'showPageSummary' => true,
                    'floatHeader' => true,
                    'pjax' => true,
                    'panel' => $panels,
                    'toolbar' => $toolbars,
                ],
                'options' => ['id' => 'Notification' . Yii::$app->user->identity->id] // a unique identifier is important
    ]);

    DynaGrid::end();
    ?>   
</div>
