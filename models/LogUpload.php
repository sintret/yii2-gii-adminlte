<?php

namespace sintret\gii\models;

use Yii;

/**
 * This is the model class for table "log_upload".
 *
 * @property integer $id
 * @property string $title
 * @property string $filename
 * @property string $fileori
 * @property string $params
 * @property string $warning
 * @property string $values
 * @property integer $type
 * @property integer $userId
 * @property string $updateDate
 * @property string $createDate
 */
class LogUpload extends \yii\db\ActiveRecord {

    const TYPE_INSERT = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    public static $type = [1 => 'Insert', 2 => 'Update', 3 => 'Delete'];
    // user model for relation with table user
    public $userModel;

    /**
     * @var UploadedFile file attribute
     */
    //public $fileori;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'log_upload';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['fileori'], 'required'],
            [['userId', 'type'], 'integer'],
            [['updateDate', 'createDate', 'params', 'warning', 'values'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['filename'], 'string', 'max' => 255],
            [['fileori'], 'file'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'filename' => 'Filename',
            'fileori' => 'File Excel (*.xls)',
            'params' => 'Params',
            'warning' => 'Warning',
            'values' => 'Values',
            'userCreate' => 'User Create',
            'userUpdate' => 'User Update',
            'updateDate' => 'Update Date',
            'createDate' => 'Create Date',
        ];
    }

    public static $imagePath = '@webroot/uploads/';

    public function getLink() {
        $file = $this->fileori;
        if ($file) {
            return Yii::getAlias($this->filename);
        }
    }

    public function getUserClassName() {
        $userClass = Yii::$app->getUser()->identityClass;
        if ($this->userModel)
            return $this->userModel;
        else
            return $userClass;
    }

    public function getUser() {
        if ($this->getUserClassName())
            return $this->hasOne($this->getUserClassName(), ['id' => 'userId']);
        else
            return null;
    }

    public function getFilenameLabel() {
        if ($this->filename) {
            return basename(Yii::getAlias($this->filename));
        }
    }

    public function getFileoriLabel() {
        if ($this->filename) {
            return basename(Yii::getAlias($this->fileori));
        }
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->createDate = date('Y-m-d H:i:s');
            $this->userId = Yii::$app->user->id;
        } else {
            $this->updateDate = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }

}
