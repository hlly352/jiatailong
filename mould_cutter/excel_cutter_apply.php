<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$applyid = fun_check_int($_GET['id']);
/** Error reporting */
error_reporting(E_ALL);
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../class/');
/** PHPExcel */
include 'PHPExcel.php';
$objPHPexcel = PHPExcel_IOFactory::load('../template_file/cutter_apply.xls');
$objWorksheet = $objPHPexcel->getActiveSheet();
$objPHPexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
$sql = "SELECT `db_cutter_apply`.`apply_number`,`db_cutter_apply`.`apply_date`,`db_employee`.`employee_name` FROM `db_cutter_apply` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` WHERE `db_cutter_apply`.`applyid` = '$applyid'";
$result = $db->query($sql);
if($result->num_rows){
	$array = $result->fetch_assoc();
	$objWorksheet->getCell('A3')->setValue("申领单号：".$array['apply_number']);
	$objWorksheet->getCell('F3')->setValue("申请日期：".$array['apply_date']);
	$sql_list = "SELECT `db_cutter_apply_list`.`cutterid`,`db_cutter_apply_list`.`quantity`,`db_cutter_apply_list`.`plan_date`,`db_cutter_apply_list`.`remark`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness` FROM `db_cutter_apply_list` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` WHERE `db_cutter_apply_list`.`applyid` = '$applyid' ORDER BY `db_cutter_apply_list`.`apply_listid` DESC";
	$result_list = $db->query($sql_list);
	$result_id = $db->query($sql_list);
	if($result_list->num_rows){
		$a = 1;
		$i = 5;
		$array_cutterid = '';
		while($row_id = $result_id->fetch_assoc()){
			$array_cutterid .= $row_id['cutterid'].',';
		}
		$array_cutterid = rtrim($array_cutterid,',');
		$sql_surplus = "SELECT `db_cutter_purchase_list`.`cutterid`,SUM(`db_cutter_order_list`.`surplus`) AS `surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`cutterid` IN ($array_cutterid) AND `db_cutter_order_list`.`surplus` > 0 GROUP BY `db_cutter_purchase_list`.`cutterid`";
		$result_surplus = $db->query($sql_surplus);
		if($result_surplus->num_rows){
			while($row_surplus = $result_surplus->fetch_assoc()){
			$array_surplus[$row_surplus['cutterid']] = $row_surplus['surplus'];
			}
		}else{
			$array_surplus = array();
		}
		while($row_list = $result_list->fetch_assoc()){
			$cutterid = $row_list['cutterid'];
			$surplus = array_key_exists($cutterid,$array_surplus)?$array_surplus[$cutterid]:0;
			$objWorksheet->getCell('A'.$i)->setValue($a);
			$objWorksheet->getCell('B'.$i)->setValue($row_list['type']);
			$objWorksheet->getCell('C'.$i)->setValue($row_list['specification']);
			$objWorksheet->getCell('D'.$i)->setValue($array_cutter_texture[$row_list['texture']]);
			$objWorksheet->getCell('E'.$i)->setValue($row_list['hardness']);
			$objWorksheet->getCell('F'.$i)->setValue($row_list['quantity']);
			$objWorksheet->getCell('G'.$i)->setValue($surplus);
			$objWorksheet->getCell('H'.$i)->setValue('件');
			$objWorksheet->getCell('I'.$i)->setValue($row_list['plan_date']);
			$objWorksheet->getCell('J'.$i)->setValue($row_list['remark']);
			$a++;
			$i++;
		}
	}
}
$objWorksheet->getCell('B'.$i)->setValue("申请人：".$array['employee_name']);
$objWorksheet->getCell('F'.$i)->setValue("审核：");
$objWorksheet->getStyle('A5:J'.($i-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
$objWorksheet->getStyle('A5:J'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
$objWorksheet->getStyle('A5:J'.($i-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
$objWorksheet->getStyle('A5:J'.($i-1))->getAlignment()->setWrapText(TRUE);
//设置字体   
$objStyle1 = $objWorksheet->getStyle('A5:J'.($i)); 
$objFont1 = $objStyle1->getFont();   
$objFont1->setName('微软雅黑','宋体');  
$objFont1->setSize(10);   
$objFont1->setBold(false);   
$objFont1->getColor()->setARGB('FF000000'); 

$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
 
$filename = "刀具申领单_".date('Y-m-j_H_i_s').".xls";
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