<?php

namespace sintret\gii\models;

use Yii;

/**
 * This is the model class for table "upload".
 *
 * @property integer $id
 * @property string $title
 * @property string $filename
 * @property string $fileori
 * @property string $params
 * @property string $warning
 * @property string $values
 * @property integer $type
 * @property integer $userCreate
 * @property integer $userUpdate
 * @property string $updateDate
 * @property string $createDate
 */
class LogUpload extends \yii\db\ActiveRecord {

    const TYPE_INSERT = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    public $userModel;
    public static $type = [1 => 'Insert', 2 => 'Update', 3 => 'Delete'];

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
            [['userCreate', 'userUpdate', 'type'], 'integer'],
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

    public function getUser() {
        if (isset($this->userModel))
            return $this->hasOne($this->userModel, ['id' => 'userId']);
    }

    public function getUserCreateLabel() {
        if ($this->userModel) {
            $user = User::find()->select('username')->where(['id' => $this->userCreate])->one();
            return $user->username;
        } else {
            return $this->userCreate;
        }
    }

    public function getUserUpdateLabel() {
        $user = User::find()->select('username')->where(['id' => $this->userUpdate])->one();
        return $user->username;
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
            $this->userCreate = Yii::$app->user->id;
            $this->userUpdate = Yii::$app->user->id;
        } else {
            $this->updateDate = date('Y-m-d H:i:s');
            $this->userUpdate = Yii::$app->user->id;
        }
        return parent::beforeSave($insert);
    }

}
