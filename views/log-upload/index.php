<?php

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use kartik\dynagrid\DynaGrid;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\LogUploadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Log Uploads';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-upload-index">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>


    <?php
    $toolbars = [
        ['content' =>
            Html::a('<i class="glyphicon glyphicon-repeat"></i>', ['log-upload/index'], ['data-pjax' => 0, 'class' => 'btn btn-default', 'title' => 'Reset Grid']) . ' '
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
        [
            'attribute' => 'type',
            'format' => 'html',
            'filter' => sintret\gii\models\LogUpload::$type,
            'value' => function ($data) {
                return sintret\gii\models\LogUpload::$type[$data->type];
            },
        ],
        'title',
        [
            'attribute' => 'fileori',
            'format' => 'raw',
            'value' => function ($data) {
                return Html::a(basename($data->fileori), Url::to('@web/uploads/' . basename($data->filename)), ['target' => '__blank']);
            },
                ],
                'warning',
//                [
//                    'attribute' => 'warning',
//                    'format' => 'html',
//                    'value' => function ($data) {
//                        return print_r(yii\helpers\Json::decode($data->warning));
//                    },
//                ],
                ['attribute' => 'userId', 'format' => 'html', 'filter' => User::dropdown(),
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
                        'options' => ['id' => 'LogUpload' . Yii::$app->user->identity->id] // a unique identifier is important
            ]);

            DynaGrid::end();
            ?>   
</div>
