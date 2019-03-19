<?php
require_once '../global_mysql_connect.php';
require_once 'shell.php';
/** Error reporting */
error_reporting(E_ALL);
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../class/');
/** PHPExcel */
include 'PHPExcel.php';

$objPHPexcel = PHPExcel_IOFactory::load('../template_file/mould.xls');
$objWorksheet = $objPHPexcel->getActiveSheet();
$objPHPexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(45);
$objWorksheet->getCell('W3')->setValue("更新日期：".date('Y-m-d'));
$sql = $_SESSION['mould']." ORDER BY `db_mould`.`mould_number` ASC,`db_mould`.`mouldid` ASC";
$result = $db->query($sql);
if($result->num_rows){
	$i = 6;
	while($row = $result->fetch_assoc()){
		$image_filedir = $row['image_filedir'];
		$image_filename = $row['image_filename'];
		$image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
		if(is_file($image_filepath)){
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			$objDrawing->setWorksheet($objPHPexcel->getActiveSheet());
			$objDrawing->setPath($image_filepath);
			$objDrawing->setHeight(40);
			$objDrawing->setWidth(72);
			$objDrawing->setOffsetX(2);
			$objDrawing->setOffsetY(12);
			$objDrawing->setCoordinates('F'.$i);
		}
		$assembler = array_key_exists($row['assembler'],$array_mould_assembler)?$array_mould_assembler[$row['assembler']]:'';
		$objWorksheet->getCell('A'.$i)->setValue($i);
		$objWorksheet->getCell('B'.$i)->setValue($row['client_code']);
		$objWorksheet->getCell('C'.$i)->setValue($row['project_name']);
		$objWorksheet->getCell('D'.$i)->setValue($row['mould_number']);
		$objWorksheet->getCell('E'.$i)->setValue($row['part_name']);
		$objWorksheet->getCell('G'.$i)->setValue($row['plastic_material']);
		$objWorksheet->getCell('H'.$i)->setValue($row['shrinkage_rate']);
		$objWorksheet->getCell('I'.$i)->setValue($row['surface']);
		$objWorksheet->getCell('J'.$i)->setValue($row['cavity_number']);
		$objWorksheet->getCell('K'.$i)->setValue($row['gate_type']);
		$objWorksheet->getCell('L'.$i)->setValue($row['core_material']);
		$objWorksheet->getCell('M'.$i)->setValue($array_is_status[$row['isexport']]);
		$objWorksheet->getCell('N'.$i)->setValue($row['quality_grade']);
		$objWorksheet->getCell('O'.$i)->setValue($row['difficulty_degree']);
		$objWorksheet->getCell('P'.$i)->setValue($row['projecter_name']);
		$objWorksheet->getCell('Q'.$i)->setValue($row['designer_name']);
		$objWorksheet->getCell('R'.$i)->setValue($row['steeler_name']);
		$objWorksheet->getCell('S'.$i)->setValue($row['electroder_name']);
		$objWorksheet->getCell('T'.$i)->setValue($assembler);
		$objWorksheet->getCell('U'.$i)->setValue($row['first_time']);
		$objWorksheet->getCell('V'.$i)->setValue($row['remark']);
		$objWorksheet->getCell('W'.$i)->setValue($row['mould_statusname']);
		$i++;
	}
}

$objWorksheet->getStyle('A5:W'.($i-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
$objWorksheet->getStyle('A5:W'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
$objWorksheet->getStyle('A5:W'.($i-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
$objWorksheet->getStyle('A5:W'.($i-1))->getAlignment()->setWrapText(TRUE);

//设置字体   
$objStyle1 = $objWorksheet->getStyle('A6:W'.($i-1)); 
$objFont1 = $objStyle1->getFont();   
$objFont1->setName('微软雅黑','宋体');  
$objFont1->setSize(10);   
$objFont1->setBold(false);   
$objFont1->getColor()->setARGB('FF000000'); 

$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
 
$filename = "模具数据_".date('Y-m-j_H_i_s').".xls";
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