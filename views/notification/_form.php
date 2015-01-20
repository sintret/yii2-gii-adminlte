<?php

use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use kartik\widgets\FileInput;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model app\models\Notification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="notification-form">

    <?php     $form = ActiveForm::begin([
                'type' => ActiveForm::TYPE_HORIZONTAL,
                'options' => ['enctype' => 'multipart/form-data']   // important, needed for file upload
    ]);?>
        
    <div class="row">
        <div class="col-md-6">
            
        </div>
        <div class="col-md-6">
            
        </div>
        
    </div>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 128]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'params')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'userUpdate')->textInput() ?>

    <?= $form->field($model, 'userCreate')->textInput() ?>

    <?= $form->field($model, 'updateDate')->textInput() ?>

    <?= $form->field($model, 'createDate')->textInput() ?>

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
