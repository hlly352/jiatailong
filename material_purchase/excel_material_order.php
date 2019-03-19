<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['id']);
/** Error reporting */
error_reporting(E_ALL);
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../class/');
/** PHPExcel */
include 'PHPExcel.php';
$objPHPexcel = PHPExcel_IOFactory::load('../template_file/material_order.xls');
$objWorksheet = $objPHPexcel->getActiveSheet();
$objPHPexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
$sql_order = "SELECT `db_material_order`.`order_number`,`db_supplier`.`supplier_name` FROM `db_material_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` WHERE `db_material_order`.`orderid` = '$orderid'";
$result_order = $db->query($sql_order);
if($result_order->num_rows){
	$array_order = $result_order->fetch_assoc();
	$sql = "SELECT `db_material_order_list`.`order_quantity`,`db_material_order_list`.`actual_quantity`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`remark`,ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2) AS `amount`,`db_material_order_list`.`process_cost`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_unit`.`unit_name`,`db_unit_actual`.`unit_name` AS `actual_unit_name` FROM `db_material_order_list` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid`= `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_order_list`.`orderid` = '$orderid' ORDER BY `db_mould`.`mould_number` DESC,`db_mould_material`.`materialid` ASC";
	$result = $db->query($sql);
	if($result->num_rows){
		$i = 6;
		$total_process_cost = 0;
		while($row = $result->fetch_assoc()){
			$objWorksheet->getCell('A'.$i)->setValue($row['mould_number']);
			$objWorksheet->getCell('B'.$i)->setValue($row['material_name']);
			$objWorksheet->getCell('C'.$i)->setValue($row['specification']);
			$objWorksheet->getCell('D'.$i)->setValue($row['texture']);
			$objWorksheet->getCell('E'.$i)->setValue($row['order_quantity']);
			$objWorksheet->getCell('F'.$i)->setValue($row['unit_name']);
			$objWorksheet->getCell('G'.$i)->setValue($row['actual_quantity']);
			$objWorksheet->getCell('H'.$i)->setValue($row['actual_unit_name']);
			$objWorksheet->getCell('I'.$i)->setValue($row['unit_price']);
			$objWorksheet->getCell('J'.$i)->setValue($row['amount']);
			$objWorksheet->getCell('K'.$i)->setValue($row['remark']);
			$total_process_cost += $row['process_cost'];
			$i++;
		}
	}
	$objWorksheet->getCell('J3')->setValue("合同号：".$array_order['order_number']);
	$objWorksheet->getCell('E4')->setValue("乙方：".$array_order['supplier_name']);
	$objWorksheet->getCell('J30')->setValue($total_process_cost);
	/*
	$objWorksheet->getStyle('A5:I'.($i-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
	$objWorksheet->getStyle('A5:I'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
	$objWorksheet->getStyle('A5:I'.($i-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
	$objWorksheet->getStyle('A5:I'.($i-1))->getAlignment()->setWrapText(TRUE);
	*/
	
	//设置字体   
	$objStyle1 = $objWorksheet->getStyle('A6:K'.($i)); 
	$objFont1 = $objStyle1->getFont();   
	$objFont1->setName('微软雅黑','宋体');  
	$objFont1->setSize(10);   
	$objFont1->setBold(false);   
	$objFont1->getColor()->setARGB('FF000000'); 
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
	 
	$filename = "物料采购订单_".date('Y-m-j_H_i_s').".xls";
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
}
?>