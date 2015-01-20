<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\builder\Form;
use kartik\datecontrol\DateControl;
use kartik\widgets\FileInput;
use kartik\widgets\SwitchInput;

$this->title='Parsing / Upload Operator excel';
$this->params['breadcrumbs'][] = ['label' => 'Operators', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
/* @var $model app\models\Operator */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="sintret-update">

    <div class="page-header">
        <h1>Parsing Excel </h1>
    </div>


    <div class="-form">


        <div class="-form">
            <?php
            $form = ActiveForm::begin([
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'options' => ['enctype' => 'multipart/form-data']   // important, needed for file upload
            ]);
            ?>

            <div class="row">
                <div class="col-md-10">
                    <?php
                    echo $form->field($model, 'fileori')->widget(FileInput::classname(), [
                        'options' => ['accept' => '.xls'],
                    ]);
                    ?>

                </div>


            </div>

            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <?=                    Html::submitButton('Upload ', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
                    ?>
                </div>
            </div>
            
            <?php
            ActiveForm::end();
            ?>

        </div>

    </div>
    <hr>
    <div class="row">
        <div class="col-md-10">
            Format Sample : <a href="<?php echo Yii::$app->urlManager->createUrl('log-upload/sample');?>">log-upload.xls</a>
        </div>

    </div>
</div>
