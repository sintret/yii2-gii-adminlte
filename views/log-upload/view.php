<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\LogUpload */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Log Uploads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-upload-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'filename',
            'fileori',
            'params:ntext',
             [
                'attribute' => 'userCreate',
                'value' => $model->userCreateLabel,
            ],
                    [
                'attribute' => 'userUpdate',
                'value' => $model->userUpdateLabel,
            ],
            
                    [
                'attribute' => 'updateDate',
                'value' => $model->updateDate,
            ],
                
                    [
                'attribute' => 'createDate',
                'value' => $model->createDate,
            ],
            
                ]]) ;?>

</div>
