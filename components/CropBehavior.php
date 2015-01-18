<?php

/*
 * Copyright (C) 2014 Sintret <sintret@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace sintret\gii\components;

use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;
use sintret\gii\components\SintretImagine;


/**
 * Description of CropBehaviour
 *
 * @author Andy Fitria <sintret@gmail.com>
 */
class CropBehavior extends Behavior {

    public $attribute = 'image';

    /* crop width default 120 */
    public $width = 200;

    /* crop height default 120 */
    public $height = 200;

    /* crop quality default 80 */
    public $quality = 80;
    public $thumb = 'thumb/';

    /**
     * List of attributes that will be handeled
     * default: get list from model rules
     * 'image'
     * ['image','image2']
     * @var string|array 
     */
    public $fileNameAttributes;

    /**
     * Is url replacement actif. If true all urls returned to model will be modified like this:
     * @webroot -> @web and @secureroot -> @secure
     * by preg_replace '/(@[a-z]*)root/' => '$1'
     * @var boolean 
     */
    public $isUrlReplacementActif = true;

    /**
     * The attribute that represents saving path.
     * default: '@webroot/'
     * All strings between {xxx} will be replaced with model values even auto-incremented primary keys.
     * '@webroot/images/thumbnail/{id}/'=>'@web/images/thumbnail/234/'
     * You can set default path AND/OR spécific path. Default path to key 0.
     * ['@webroot/images/all/{id}/','thumbnail'=>'@webroot/images/thumbnail/{id}/']
     * @var string|array 
     */
    public $paths = '@webroot/';

    /**
     * Determies if fullpath or filename will be assigned to model.
     * default: true
     * You can set default returnFullPath AND/OR spécific returnFullPath. Default path to key 0.
     * [true,'thumbnail'=>false]
     * @var boolean|array 
     */
    public $returnFullPath = true;

    /**
     * Default value for skipOnEmpty. If field is submitted empty it keeps the old value 
     * default: true
     * You can set default skipOnEmpty AND/OR spécific skipOnEmpty. Default path to key 0.
     * [true,'thumbnail'=>false]
     * if fileNameAttributes is not in behavior config but loaded from model rules, rules settings will be predominant
     * @var boolean|array  
     */
    public $skipOnEmpty = true;

    /*
     * private vars for internal manipulations
     */
    private $configArray;
    private $files;
    private $oldUrls;
    private $isActif = false;

    /**
     * Registers Events only if loadWithFiles is called to avoid conflict with SearchModels
     * @return array event list
     */
    public function events() {
        /* @var $model ActiveRecord */
        return($this->isActif) ? [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
                ] : [];
    }

    /**
     * Entry script
     * @param array $data
     * @param string $formName
     * @see Model::load()
     */
    public function loadWithFiles($data, $formName = null) {
        /* @var $model ActiveRecord */
        $model = $this->owner;
        $this->beforeLoad();
        if ($model->load($data, $formName)) {
            // activate listeners
            $this->activateEvents();
            return true;
        }
        return false;
    }

    /**
     * Registers Events
     */
    private function activateEvents() {
        /* @var $model ActiveRecord */
        $model = $this->owner;
        $this->isActif = true;
        $this->attach($model);
    }

    /**
     * Unregisters Events
     */
    private function disableEvents() {
        /* @var $model ActiveRecord */
        $model = $this->owner;
        $this->isActif = false;
        $this->attach($model);
    }

    /**
     * Before load registers old file Names.
     * Called from 
     */
    public function beforeLoad() {
        /* @var $model ActiveRecord */
        $model = $this->owner;
        //make config file
        $this->setCongig();
        // keep old urls
        if (!$model->isNewRecord) {
            foreach ($this->fileNameAttributes as $value) {
                $this->oldUrls[$value] = $model->{$value};
            }
        }
    }

    /**
     * Before validate event.
     * Gets new file instances.
     */
    public function beforeValidate() {

        /* @var $model ActiveRecord */
        $model = $this->owner;
        // get uploaded files instances
        foreach ($this->fileNameAttributes as $value) {
            $this->files[$value] = UploadedFile::getInstance($model, $value);
            if ($this->files[$value]) {
                $path = $this->resolveUrl($this->configArray[$value][0]);
                $model->{$value} = (($this->configArray[$value][1]) ? $path : '') . $this->files[$value]->name;
            }
        }
    }

    /**
     * After validate event
     * Assign old value if skipOnEmpty and new value empty
     */
    public function afterValidate() {
        /* @var $model ActiveRecord */
        $model = $this->owner;
        if (!$model->isNewRecord) {
            foreach ($this->fileNameAttributes as $value) {
                if (empty($model->{$value}) && !empty($this->oldUrls[$value]) && $this->skipOnEmpty[$value]) {
                    $model->{$value} = $this->oldUrls[$value];
                }
            }
        }
    }

    /**
     * After insert event
     */
    public function afterInsert() {
        $this->afterSave();
        $this->updateDbOnInsert();
    }

    /**
     * After update event
     */
    public function afterUpdate() {
        $this->afterSave();
    }

    /**
     * Saves files to definitive position
     */
    public function afterSave() {
        // last listener has been call so we can disable event
        $this->disableEvents();
        $model = $this->owner;

        foreach ($this->fileNameAttributes as $value) {
            if ($this->files[$value]) {
                $path = $this->resolvePath($this->configArray[$value][0]);
                $folder = Yii::getAlias($path);
                if (!is_dir($folder)) {
                    FileHelper::createDirectory($folder);
                }
                $thumb = Yii::getAlias($path . $this->thumb);
                if (!is_dir($thumb)) {
                    FileHelper::createDirectory($thumb);
                }

                $name = $this->files[$value]->name;
                if ($this->files[$value]->saveAs($folder . $name)) {

                    SintretImagine::thumbnail($folder . $name, $this->width, $this->height)
                            ->save(Yii::getAlias($thumb . $name), ['quality' => $this->quality]);
                }
            }
        }
    }

    /**
     * replaces '/(@[a-z]*)root/' => '$1' if isUrlReplacementActif
     * @param string $path
     * @return string
     */
    public function resolveUrl($path) {

        $path = $this->resolvePath($path);
        return ($this->isUrlReplacementActif) ? preg_replace('/(@[a-z]*)root/', '$1', $path) : $path;
    }

    /**
     * adds varibables to path
     * @param string $path
     * @return string
     */
    public function resolvePath($path) {
        $model = $this->owner;
        return preg_replace_callback(
                '|{(.*?)}|', function ($matches) use ($model) {
            return ($model->{$matches[1]}) ? $model->{$matches[1]} : $matches[0];
        }, $path
        );
    }

    /**
     * Makes initial config for file upload
     */
    public function setCongig() {
        $model = $this->owner;
        /* $this->skipOnEmpty is booleans cast as array */
        if (is_bool($this->skipOnEmpty)) {
            $this->skipOnEmpty = (array) $this->skipOnEmpty;
        }

        /* $this->fileNameAttributes is string cast as array */
        if (is_string($this->fileNameAttributes)) {
            $this->fileNameAttributes = (array) $this->fileNameAttributes;
        }
        /* if fileNameAttributes is not set try to get it from rules */
        if (!isset($this->fileNameAttributes)) {
            foreach ($model->rules() as $rule) {
                if ($rule[1] == 'file') {

                    $attributes = (array) $rule[0];
                    $this->fileNameAttributes = array_merge((array) $this->fileNameAttributes, $attributes);
                    foreach ($attributes as $value) {
                        $this->skipOnEmpty[$value] = (isset($rule['skipOnEmpty'])) ? $rule['skipOnEmpty'] : $this->skipOnEmpty[0];
                    }
                }
            }
        }
        /* if still no fileNameAttributes throw an error */
        if (!isset($this->fileNameAttributes)) {
            throw new InvalidConfigException(Yii::t('app', 'The "fileNameAttributes" must be set. No file validation rules() found in your model. like: [[\'image\',\'image2\'], \'file\', \'extensions\' => \'jpg\'] or set it manually for "{behavior}"', ['behavior' => __CLASS__]));
        }

        /* if $this->paths is string cast as array */
        if (is_string($this->paths)) {
            $this->paths = (array) $this->paths;
        }

        /* $this->returnFullPath is boolean cast as array */
        if (is_bool($this->returnFullPath)) {
            $this->returnFullPath = (array) $this->returnFullPath;
        }

        /**
         *  make config array
         */
        $this->configArray = [];

        foreach ($this->fileNameAttributes as $value) {
            /* set foreach $this->fileNameAttributes a skipOnEmpty */
            if (!isset($this->skipOnEmpty[$value])) {
                if (!isset($this->skipOnEmpty[0])) {
                    throw new InvalidConfigException(Yii::t('app', 'Missing "skipOnEmpty" for "{item}" in {behavior} and no default "skipOnEmpty" is set.', ['item' => $value, 'behavior' => __CLASS__]));
                }
                $this->skipOnEmpty[$value] = $this->skipOnEmpty[0];
            }
            /* set foreach $this->fileNameAttributes a returnFullPath */
            if (!isset($this->returnFullPath[$value])) {
                if (!isset($this->returnFullPath[0])) {
                    throw new InvalidConfigException(Yii::t('app', 'Missing "returnFullPath" for "{item}" in {behavior} and no default "returnFullPath" is set.', ['item' => $value, 'behavior' => __CLASS__]));
                }
                $this->returnFullPath[$value] = $this->returnFullPath[0];
            }
            /*
             * set foreach $this->fileNameAttributes a path
             * 
             */
            if (!isset($this->paths[$value])) {
                /*
                 * for $value -> image 
                 * $paths -> ['image'=>'@webroot/images/','thumbnail'=>'@webroot/thumb/','@webroot/default/']
                 * returns '@webroot/images/'
                 */
                if (!isset($this->paths[0])) {
                    throw new InvalidConfigException(Yii::t('app', 'Missing path for "{item}" in {behavior} and no default path is set.', ['item' => $value, 'behavior' => __CLASS__]));
                    /*
                     * for $value -> image 
                     * $paths -> ['thumbnail'=>'@webroot/thumb/','@webroot/default/']
                     * returns '@webroot/default/'
                     */
                }
                $this->paths[$value] = $this->paths[0];
            }
            /*
             * set foreach $this->fileNameAttributes a path
             * $key=>[path,returnFullPath,skipOnEmpty] 
             */
            $this->configArray[$value] = [$this->paths[$value], $this->returnFullPath[$value], $this->skipOnEmpty[$value]];
        }
    }

    /**
     * update db entry after save if necessary (pk replacement)
     */
    private function updateDbOnInsert() {
        /* @var $model ActiveRecord */
        $model = $this->owner;

        // update model and db after insert for autoincrement PK in file url
        $schema = $model->getTableSchema();
        $modifications;
        foreach ($this->fileNameAttributes as $value) {
            if ($this->files[$value]) {
                // get new value
                $newValue = $this->resolveUrl($this->configArray[$value][0]) . $this->files[$value]->name;

                if ($model->{$value} != $newValue) {
                    // if new value set model
                    $model->{$value} = $newValue;
                    if ($schema->getColumn($value)) {
                        // if in schema column add to modifications for db update
                        $modifications[$value] = $newValue;
                    }
                }
            }
        }
        if (isset($modifications)) {
            // if modifications update db
            $db = $model->getDb();
            $where = '';
            foreach ($model->getPrimaryKey(true) as $k => $v) {
                if (!empty($where)) {
                    $where .= ' AND ';
                }
                $where.=$k . '=' . $v;
            }
            $db->createCommand()->update($schema->name, $modifications, $where)->execute();
        }
    }

}
