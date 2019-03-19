<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['orderid']);
/** Error reporting */
error_reporting(E_ALL);
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../class/');
/** PHPExcel */
include 'PHPExcel.php';
$objPHPexcel = PHPExcel_IOFactory::load('../template_file/cutter_order.xls');
$objWorksheet = $objPHPexcel->getActiveSheet();
$objPHPexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
$sql_order = "SELECT `db_cutter_order`.`order_number`,`db_supplier`.`supplier_name` FROM `db_cutter_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` WHERE `db_cutter_order`.`orderid` = '$orderid'";
$result_order = $db->query($sql_order);
if($result_order->num_rows){
	$array_order = $result_order->fetch_assoc();
	$sql = "SELECT `db_cutter_order_list`.`unit_price`,`db_cutter_order_list`.`remark`,`db_cutter_purchase_list`.`quantity`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,(`db_cutter_purchase_list`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` WHERE `db_cutter_order_list`.`orderid` = '$orderid' ORDER BY `db_cutter_specification`.`typeid` DESC,`db_cutter_hardness`.`texture` DESC,`db_mould_cutter`.`cutterid` ASC";
	$result = $db->query($sql);
	if($result->num_rows){
		$i = 6;
		while($row = $result->fetch_assoc()){
			$objWorksheet->getCell('A'.$i)->setValue($row['type']);
			$objWorksheet->getCell('B'.$i)->setValue($row['specification']);
			$objWorksheet->getCell('C'.$i)->setValue($array_cutter_texture[$row['texture']]);
			$objWorksheet->getCell('D'.$i)->setValue($row['hardness']);
			$objWorksheet->getCell('E'.$i)->setValue($row['brand']);
			$objWorksheet->getCell('F'.$i)->setValue($row['quantity']);
			$objWorksheet->getCell('G'.$i)->setValue('件');
			$objWorksheet->getCell('H'.$i)->setValue($row['unit_price']);
			$objWorksheet->getCell('I'.$i)->setValue($row['amount']);
			$objWorksheet->getCell('J'.$i)->setValue($row['remark']);
			$i++;
		}
	}
	$objWorksheet->getCell('I3')->setValue("合同号：".$array_order['order_number']);
	$objWorksheet->getCell('F4')->setValue("乙方：".$array_order['supplier_name']);
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
	 
	$filename = "刀具采购订单_".date('Y-m-j_H_i_s').".xls";
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