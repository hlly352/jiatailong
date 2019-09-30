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
$objPHPexcel = PHPExcel_IOFactory::load('../template_file/other_material_order.xls');
$objWorksheet = $objPHPexcel->getActiveSheet();
$objPHPexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
$sql_order = "SELECT `db_other_material_order`.`order_number`,`db_supplier`.`supplier_name` FROM `db_other_material_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_other_material_order`.`supplierid` WHERE `db_other_material_order`.`orderid` = '$orderid'";
$result_order = $db->query($sql_order);
if($result_order->num_rows){
	$array_order = $result_order->fetch_assoc();
	// $sql = "SELECT `db_other_material_data`.`material_name`,`db_mould_other_material`.`material_specification`,`db_other_material_orderlist`.`actual_quantity`,`db_other_material_orderlist`.`unit_price`,`db_other_material_orderlist`.`amount`,`db_other_material_orderlist`.`remark`,`db_mould_other_material`.`unit` FROM `db_other_material_orderlist` INNER JOIN `db_mould_other_material` ON `db_mould_other_material`.`mould_other_id` = `db_other_material_orderlist`.`materialid` INNER JOIN `db_other_material_data` ON `db_mould_other_material`.`material_name` = `db_other_material_data`.`dataid` WHERE `db_other_material_orderlist`.`orderid` = '$orderid'";
$sql = "SELECT `db_other_material_data`.`material_name` AS `data_name`,`db_other_material_specification`.`material_name`,`db_other_material_data`.`unit`,`db_mould_other_material`.`unit` AS `material_unit`,`db_other_material_specification`.`specification_name`,`db_mould_other_material`.`quantity`,`db_other_material_orderlist`.`actual_quantity`,`db_other_material_orderlist`.`unit_price`,`db_other_material_orderlist`.`tax_rate`,(`db_other_material_orderlist`.`actual_quantity` * `db_other_material_orderlist`.`unit_price`) AS `amount`,`db_other_material_orderlist`.`iscash`,`db_other_material_orderlist`.`plan_date`,`db_other_material_orderlist`.`remark` FROM `db_other_material_orderlist`  INNER JOIN `db_mould_other_material` ON `db_other_material_orderlist`.`materialid` = `db_mould_other_material`.`mould_other_id` LEFT JOIN `db_other_material_specification` ON `db_mould_other_material`.`material_name` = `db_other_material_specification`.`specificationid` LEFT JOIN `db_other_material_data` ON `db_other_material_specification`.`materialid` = `db_other_material_data`.`dataid`  WHERE `db_other_material_orderlist`.`orderid` = '$orderid'";

	$result = $db->query($sql);
	if($result->num_rows){
		$i = 6;
		$total_amount = 0;
		while($row = $result->fetch_assoc()){
			$objWorksheet->getCell('A'.$i)->setValue($i-5);
			$objWorksheet->getCell('B'.$i)->setValue($row['material_unit']?$row['material_name']:$row['data_name']);
			$objWorksheet->getCell('C'.$i)->setValue($row['specification_name']);
			$objWorksheet->getCell('D'.$i)->setValue($row['actual_quantity']);
			$objWorksheet->getCell('F'.$i)->setValue($row['material_unit']?$row['material_unit']:$row['unit']);
			$objWorksheet->getCell('E'.$i)->setValue($row['unit_price']);
			$objWorksheet->getCell('G'.$i)->setValue($row['amount']);
			$objWorksheet->getCell('H'.$i)->setValue($row['remark']);
			$i++;
		 $total_amount += $row['amount'];	
		}
	}
	
	$objWorksheet->getCell('G3')->setValue("合同号：".$array_order['order_number']);
	$objWorksheet->getCell('D4')->setValue("乙方：".$array_order['supplier_name']);
	$objWorksheet->getCell('G29')->setValue($total_amount);
	/*
	$objWorksheet->getStyle('A5:I'.($i-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
	$objWorksheet->getStyle('A5:I'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
	$objWorksheet->getStyle('A5:I'.($i-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
	$objWorksheet->getStyle('A5:I'.($i-1))->getAlignment()->setWrapText(TRUE);
	*/
	
	//设置字体   
	$objStyle1 = $objWorksheet->getStyle('A6:H12'); 
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