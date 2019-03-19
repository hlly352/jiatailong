<?php
require_once '../global_mysql_connect.php';
require_once 'shell.php';
/** Error reporting */
error_reporting(E_ALL);
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../class/');
/** PHPExcel */
include 'PHPExcel.php';
$objPHPexcel = PHPExcel_IOFactory::load('../template_file/material_inquiry.xls');
$objWorksheet = $objPHPexcel->getActiveSheet();
$objPHPexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);

$sql = $_SESSION['material_inquiry_list'];
$result = $db->query($sql);
if($result->num_rows){
	$i = 6;
	while($row = $result->fetch_assoc()){
		$objWorksheet->getCell('A'.$i)->setValue($row['mould_number']);
		$objWorksheet->getCell('B'.$i)->setValue($row['material_name']);
		$objWorksheet->getCell('C'.$i)->setValue($row['specification']);
		$objWorksheet->getCell('D'.$i)->setValue($row['texture']);
		$objWorksheet->getCell('E'.$i)->setValue($row['material_quantity']);
		$objWorksheet->getCell('I'.$i)->setValue($row['remark']);
		$i++;
	}
}
$objWorksheet->getCell('B'.$i)->setValue("编制：");
$objWorksheet->getCell('D'.$i)->setValue("审核：");
$objWorksheet->getCell('G'.$i)->setValue("价格批准：");
$objWorksheet->getStyle('A5:I'.($i-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
$objWorksheet->getStyle('A5:I'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
$objWorksheet->getStyle('A5:I'.($i-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
$objWorksheet->getStyle('A5:I'.($i-1))->getAlignment()->setWrapText(TRUE);

//设置字体   
$objStyle1 = $objWorksheet->getStyle('A6:I'.($i)); 
$objFont1 = $objStyle1->getFont();   
$objFont1->setName('微软雅黑','宋体');  
$objFont1->setSize(10);   
$objFont1->setBold(false);   
$objFont1->getColor()->setARGB('FF000000'); 

$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
 
$filename = "物料询价单_".date('Y-m-j_H_i_s').".xls";
//$filePath = "../upload/tmp/";
//$path = $filePath.$filename;
//$objWriter->save($path);

header("Content-Type: application/force-download");   
header("Content-Type: application/octet-stream");   
header("Content-Type: application/download");   
header('Content-Disposition:inline;filename="'.$filename.'"');   
header("Content-Transfer-Encoding: binary");   
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");   
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");   
header("Pragma: no-cache");   
$objWriter->save('php://output');
?>