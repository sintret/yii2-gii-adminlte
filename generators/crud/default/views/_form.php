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
use mihaildev\ckeditor\CKEditor;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

    <?= "<?php " ?>
    $form = ActiveForm::begin([
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'options' => ['enctype' => 'multipart/form-data']   // important, needed for file upload
    ]);?>


        <?php
        $fields = [];
        $num = 1;
        $count = $generator->getColumnNames();
        $exception = ['userUpdate','createDate','updateDate','userCreate'];
        foreach ($generator->getColumnNames() as $attribute) {
            $column = $generator->getTableSchema()->columns[$attribute];
            $type = $generator->getTableSchema()->columns[$attribute]->type;
                   
            if (in_array($attribute, $safeAttributes)) {
                if ($num % 2 == 0){
                    $l ='left';
                } else {
                    $l ='right';
                }

                
                if ($attribute == 'image') {
                    $fields[$l][] =  '<?php
                    $plugins = ["options"=>["accept"=>"image/*"]];
                    if ($model->image) {
                        $plugins = [
                                "options"=>["accept"=>"image/*"],
                                "pluginOptions" => ["initialPreview" => [kartik\helpers\Html::img($model->thumbnailTrue, ["class" => "file-preview-image"])]]
                                ];
                     }
                    echo $form->field($model, "image")->widget(FileInput::classname(),$plugins);
                    ?>';
                } elseif($attribute == 'description') {
                    $fields[$l][] =  '<?= $form->field($model, "description")->widget(CKEditor::className(), ["editorOptions" => [ "preset" => "full", "inline" => false]]);?>';
                }elseif ($type=='date'){
                    $fields[$l][] = '<?=
            $form->field($model, "release")->widget(DatePicker::classname(), [
                "options" => ["placeholder" => "Enter date ..."],
                "pluginOptions" => [
                    "autoclose" => true,
                    "format" => "yyyy-mm-dd"
                ]
            ])
            ?>';
                } else {
                    if(!in_array($attribute, $exception))
                        $fields[$l][] = "\n            <?= " . $generator->generateActiveField($attribute) . " ?>\n";
                }
                $num++;
            }

        }
        ?>
    <div class="row">
        <div class="col-md-6">
        <?php if($fields['left']) foreach ($fields['left'] as $val) {
           echo $val;
        }
        ?>
        </div>

        <div class="col-md-6">
        <?php if($fields['right'])  foreach ($fields['right'] as $val) {
           echo $val;
        }
        ?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        <?= "        <?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Create') ?> : <?= $generator->generateString('Update') ?>, ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

        <?= "<?php " ?>ActiveForm::end(); ?>

</div>
