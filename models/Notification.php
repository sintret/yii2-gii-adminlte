<?php

namespace sintret\gii\models;

use Yii;
use kartik\helpers\Enum;

/**
 * This is the model class for table "notification".
 *
 * @property integer $id
 * @property string $title
 * @property string $message
 * @property string $url
 * @property string $params
 * @property integer $userId
 * @property string $updateDate
 * @property string $createDate
 */
class Notification extends \yii\db\ActiveRecord {

    // user model for relation with table user
    public $userModel;
    public static $icon = ['User' => 'fa fa-users', 'Member' => 'fa fa-users', 'Upload' => 'fa fa-upload', 'Order' => 'fa fa-shopping-cart'];

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'notification';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['message', 'params'], 'string'],
            [['userId'], 'integer'],
            [['updateDate', 'createDate'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['url'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'message' => 'Message',
            'url' => 'Url',
            'params' => 'Params',
            'userId' => 'User',
            'updateDate' => 'Update Date',
            'createDate' => 'Create Date',
        ];
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

    public static function count() {
        return static::find()->count();
    }

    public static function notification($array = NULL) {
        $models = static::find()->orderBy('id desc')->all();
        $return = '';
        $return .='<li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-warning"></i>
                                <span class="label label-warning">' . self::count() . '</span>
                            </a>';
        $return .= '<ul class="dropdown-menu">
                                <li class="header">You have ' . self::count() . ' notifications</li>
                                <li>
                                    <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 200px;">
                                    <ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">';
        if ($models)
            foreach ($models as $model) {
                $params = $model->params;
                if ($params) {
                    $json = json_decode($params, true);
                    if (array_key_exists("link", $json)) {
                        $link = $json['link'];
                        if ($link) {
                            if (isset($link['controller'])) {
                                $url = \yii\helpers\Url::to([$link['controller'] . '/' . $link['method'], 'id' => $json['id']]);
                            } else {
                                $url = $link;
                            }
                        }
                    } else {
                        $url = '#';
                    }
                }

                $return .='<li><a href="' . $url . '">' . self::icon($params, $array) . ' ' . $model->message . ' <small style="color:green">' . $model->timeago . '</small></a></li>';
            }
        $return .='</ul><div class="slimScrollBar" style="width: 3px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 0px; z-index: 99; right: 1px; height: 156.862745098039px; background: rgb(0, 0, 0);"></div><div class="slimScrollRail" style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 0px; opacity: 0.2; z-index: 90; right: 1px; background: rgb(51, 51, 51);"></div></div>';
        $return .=' <li class="footer"><a href="' . \yii\helpers\Url::to(["/notification/index"]) . '">View all</a></li></ul></li>';

        return $return;
    }

    public function getTimeago() {
        if ($this->createDate)
            return Enum::timeElapsed($this->createDate);
    }

    public static function icon($json, $array = NULL) {
        $model = '';
        $return = '';
        $icon = '';
        if ($json)
            $params = json_decode($json, true);
        if ($params) {
            $model = $params['model'];
        }
        if ($array) {
            $icon = $array[$model];
        } else {
            if (array_key_exists($model, self::$icon)) {
                $icon = self::$icon[$model];
            } else {
                $icon = 'fa fa-envelope';
            }
        }
        return '<i class="' . $icon . '"></i>';
    }

}
