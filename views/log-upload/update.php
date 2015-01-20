<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LogUpload */

$this->title = 'Update Log Upload: ' . ' ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Log Uploads', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="log-upload-update">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
