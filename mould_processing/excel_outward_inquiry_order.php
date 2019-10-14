<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$inquiry_orderid = fun_check_int($_GET['id']);
/** Error reporting */
error_reporting(E_ALL);
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../class/');
/** PHPExcel */
include 'PHPExcel.php';
$objPHPexcel = PHPExcel_IOFactory::load('../template_file/outward_inquiry_order.xls');
$objWorksheet = $objPHPexcel->getActiveSheet();
$objPHPexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
$sql_order = "SELECT `db_outward_inquiry_order`.`inquiry_number`,`db_outward_inquiry_order`.`inquiry_date`,`db_supplier`.`supplier_name`,`db_supplier`.`supplier_address`,`db_supplier`.`supplier_tel`,`db_employee`.`employee_name` FROM `db_outward_inquiry_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_outward_inquiry_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_outward_inquiry_order`.`employeeid` WHERE `db_outward_inquiry_order`.`inquiry_orderid` = '$inquiry_orderid'";
$result_order = $db->query($sql_order);
if($result_order->num_rows){
	$array_order = $result_order->fetch_assoc();
$sql = "SELECT `db_employee`.`employee_name`,`db_mould_outward_type`.`outward_typename`,`db_outward_inquiry_orderlist`.`plan_date`,`db_outward_inquiry_orderlist`.`listid`,`db_outward_inquiry`.`outward_quantity`,`db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_outward_inquiry`.`outward_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_outward_inquiry`.`outward_remark`,`db_mould_material`.`complete_status`,`db_mould_specification`.`mould_no`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code` FROM `db_outward_inquiry_orderlist` INNER JOIN `db_outward_inquiry` ON `db_outward_inquiry_orderlist`.`inquiryid` = `db_outward_inquiry`.`inquiryid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_outward_inquiry`.`materialid` INNER JOIN `db_employee` ON `db_outward_inquiry`.`employeeid` = `db_employee`.`employeeid` INNER JOIN `db_mould_specification` ON `db_mould_specification`.`mould_specification_id` = `db_mould_material`.`mouldid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_outward_inquiry`.`outward_typeid` WHERE `db_outward_inquiry_orderlist`.`inquiry_orderid` = '$inquiry_orderid'  ORDER BY `db_mould_specification`.`mould_no` DESC,`db_mould_material`.`materialid` ASC";

	$result = $db->query($sql);
	if($result->num_rows){
		$i = 6;
		$total_amount = 0;
		while($row = $result->fetch_assoc()){
			$objWorksheet->getCell('A'.$i)->setValue($row['mould_no']);
			$objWorksheet->getCell('B'.$i)->setValue($row['material_name']);
			$objWorksheet->getCell('C'.$i)->setValue($row['material_number']);
			$objWorksheet->getCell('D'.$i)->setValue($row['specification']);
			$objWorksheet->getCell('E'.$i)->setValue($row['texture']);
			$objWorksheet->getCell('F'.$i)->setValue($row['hardness']);
			$objWorksheet->getCell('G'.$i)->setValue($row['brand']);
			$objWorksheet->getCell('H'.$i)->setValue($row['outward_typename']);
			$objWorksheet->getCell('I'.$i)->setValue($row['outward_quantity']);
			$objWorksheet->getCell('L'.$i)->setValue($row['outward_remark']);
			$i++;
		}
	}
	$j = $i + 1;
	   $objWorksheet->getCell('K3')->setValue("询价单号：".$array_order['inquiry_number']);
	   $objWorksheet->getCell('I4')->setValue("乙方：".$array_order['supplier_name']);
    	//$objWorksheet->mergeCells('A'.$i.':G'.$i);
	
	  $objWorksheet->getStyle('A5:L'.($i-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
	  $objWorksheet->getStyle('A5:L'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
	  $objWorksheet->getStyle('A5:L'.($i-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
	  $objWorksheet->getStyle('A5:L'.($i-1))->getAlignment()->setWrapText(TRUE);

	$objWorksheet->mergeCells('A'.$j.':L'.$j);
	$objWorksheet->mergeCells('A'.($j+1).':L'.($j+1));
	$objWorksheet->mergeCells('A'.($j+2).':L'.($j+2));
	$objWorksheet->mergeCells('A'.($j+3).':L'.($j+3));
	$objWorksheet->mergeCells('A'.($j+4).':L'.($j+4));
	$objWorksheet->mergeCells('A'.($j+5).':L'.($j+5));
	$objWorksheet->mergeCells('A'.($j+6).':D'.($j+6));
	$objWorksheet->mergeCells('F'.($j+6).':L'.($j+6));
	$objWorksheet->mergeCells('A'.($j+7).':D'.($j+7));
	$objWorksheet->mergeCells('F'.($j+7).':L'.($j+7));
	$objWorksheet->mergeCells('A'.($j+8).':D'.($j+8));
	$objWorksheet->mergeCells('F'.($j+8).':L'.($j+8));
	$objWorksheet->mergeCells('A'.($j+9).':D'.($j+9));
	$objWorksheet->mergeCells('F'.($j+9).':L'.($j+9));
	$objWorksheet->mergeCells('A'.($j+10).':D'.($j+10));
	$objWorksheet->mergeCells('F'.($j+10).':L'.($j+10));

	 $objWorksheet->getCell('A'.($j))->setValue("备注：1.以上价格含3%增值税；");
	 $objWorksheet->getCell('A'.($j+1))->setValue("2.结算方式：");
	 $objWorksheet->getCell('A'.($j+2))->setValue("3.乙方送货至甲方仓库，送货时请务必附出货检验报告、送货单、外协询价单，并将外协询价单送到生产文员处，否则拒收。");
	 $objWorksheet->getCell('A'.($j+3))->setValue("因未按计划到货或品质不符而造成的直接及间接经济损失由乙方承担；");
	 $objWorksheet->getCell('A'.($j+4))->setValue("4.如有合同纠纷，由苏州仲裁委员会仲裁解决；");
	 $objWorksheet->getCell('A'.($j+5))->setValue("5.此询价单经双方签字盖章后才具有法律效应，传真、扫描件同样具有法律效应。");
	 $objWorksheet->getCell('A'.($j+6))->setValue("甲方：苏州希尔林机械科技有限公司");
	 $objWorksheet->getCell('F'.($j+6))->setValue("乙方：".$array_order['supplier_name']);
	 $objWorksheet->getCell('A'.($j+7))->setValue("地址：苏州市吴中区木渎镇藏中路1429号");
	 $objWorksheet->getCell('F'.($j+7))->setValue("地址：".$array_order['supplier_address']);
	 $objWorksheet->getCell('A'.($j+8))->setValue("电话：0512-66313708");
	 $objWorksheet->getCell('F'.($j+8))->setValue("电话：".$array_order['supplier_tel']);
	 $objWorksheet->getCell('A'.($j+9))->setValue("传真：0512-66310728");
	 $objWorksheet->getCell('F'.($j+9))->setValue("传真：");
	 $objWorksheet->getCell('A'.($j+10))->setValue("签字盖章：");
	 $objWorksheet->getCell('F'.($j+10))->setValue("签字盖章：");
	 $objWorksheet->getStyle('A'.$j.':J'.($j+10))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	//设置字体   
	$objStyle1 = $objWorksheet->getStyle('A6:H'.($i)); 
	$objFont1 = $objStyle1->getFont();   
	$objFont1->setName('微软雅黑','宋体');  
	$objFont1->setSize(10);   
	$objFont1->setBold(false);   
	$objFont1->getColor()->setARGB('FF000000');  
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
	 
	$filename = "外协加工询价单_".date('Y-m-j_H_i_s').".xls";
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