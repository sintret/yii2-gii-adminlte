<?php

/**
 * Description of SintretImagine, fixed bug with nginx and hhvm
 *
 * @author Andy Fitria <sintret@gmail.com>
 */
namespace sintret\gii\components;

use yii\imagine\Image;

class SintretImagine extends Image {

    public static $driver = [self::DRIVER_GD2, self::DRIVER_GMAGICK, self::DRIVER_IMAGICK];

}
