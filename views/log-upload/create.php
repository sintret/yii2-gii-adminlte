<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\LogUpload */

$this->title = 'Create Log Upload';
$this->params['breadcrumbs'][] = ['label' => 'Log Uploads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-upload-create">

    <div class="page-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
