<?php

$sheet = $objPHPExcel->setActiveSheetIndex(0);

$num = 0;
foreach ($attributes as $k => $v) {
    $sheet->setCellValue($excelChar[$num] .'1', $k);
    $sheet->setCellValue($excelChar[$num] .'2', $v);
    $num++;
}


$start = 4;
if ($models)
    foreach ($models as $model) {
        $num = 0;
        foreach ($attributes as $k => $v) {
            $sheet->setCellValue($excelChar[$num] . $start, $model->$k);
            $num++;
        }
        $start++;
    }
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$name.'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
