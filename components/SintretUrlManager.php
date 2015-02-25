<?php

namespace sintret\gii\components;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use yii\web\UrlManager;

class SintretUrlManager extends UrlManager {

    public function createUrl($params) {
        if (isset($params['title']))
            $params['title'] = $this->clean(self::normal_chars($params['title']));

        return parent::createUrl($params);
    }

    public function clean($name) {

        return strtolower(str_replace(" ", "_", $name));
    }

    public static function normal_chars($string) {
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');
        $string = preg_replace('~&([a-z]{1,2})(acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i', '$1', $string);
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        $string = preg_replace(array('~[^0-9a-z]~i', '~[ -]+~'), ' ', $string);

        return trim($string, ' -');
    }

}
