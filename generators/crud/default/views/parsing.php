<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>
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
<?php echo "?>\n";?>
<div class="sintret-update">

    <div class="page-header">
        <h1>Parsing Excel <?= $modelClass ?></h1>
    </div>


    <div class="<?= $modelClass ?>-form">


        <div class="<?= $modelClass ?>-form">
            <?php echo "<?php\n";?>
            $form = ActiveForm::begin([
                        'type' => ActiveForm::TYPE_HORIZONTAL,
                        'options' => ['enctype' => 'multipart/form-data']   // important, needed for file upload
            ]);
            <?php echo "?>\n";?>

            <div class="row">
                <div class="col-md-10">
                    <?php echo "<?php\n";?>
                    echo $form->field($model, 'fileori')->widget(FileInput::classname(), [
                        'options' => ['accept' => '.xls'],
                    ]);
                    <?php echo "?>\n";?>

                </div>


            </div>

            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <?php echo "<?=";?>
                    Html::submitButton('Upload ', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
                    <?php echo "?>\n";?>
                </div>
            </div>
            <div class="notifications"></div>
            
            <?php echo "<?php\n";?>
            ActiveForm::end();
            <?php echo "?>\n";?>

        </div>

    </div>
    <hr>
    <div class="row">
        <div class="col-md-10">
            Format Sample : <a href="<?php echo "<?php";?> echo Yii::$app->urlManager->createUrl('<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>/sample');<?php echo "?>";?>"><?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>.xls</a>
        </div>

    </div>
</div>

<?php echo "<?php \n";?>
if(Yii::$app->session->get($log)){
$this->registerJs('$(document).ready(function(){ $.ajax({
        type:"POST",
        url:"' . Yii::$app->urlManager->createUrl([$route,'id'=>Yii::$app->session->get($log)]) . '",
        beforeSend:function(){ $("#notifications").html("'.yii\helpers\Url::to("@web/img/loadingAnimation.gif").'");},
        success:function(html){
            $(".notifications").html(html);
        }
    });});');
 } 
 Yii::$app->session->set($log,NULL);
<?php echo "?> \n";?>