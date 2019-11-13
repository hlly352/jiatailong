<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$array_designid = $_GET['designid'];
$designid = fun_convert_checkbox($array_designid);
/** Error reporting */
error_reporting(E_ALL);
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../class/');
/** PHPExcel */
include 'PHPExcel.php';
$objPHPexcel = PHPExcel_IOFactory::load('../template_file/design_plan.xls');
$objWorksheet = $objPHPexcel->getActiveSheet();
$objPHPexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
$sql = "SELECT *,`db_drawer`.`employee_name` AS `drawer_2d`,`db_design_group`.`employee_name` AS `design_group`,`db_projecter`.`employee_name` AS `projecter`,`db_designer`.`employee_name` AS `designer`,`db_mould_specification`.`mould_specification_id`,`db_mould_specification`.`image_filepath`,`db_mould_specification`.`material_specification`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`material_other`,`db_mould_specification`.`mould_name`,`db_mould_data`.`upload_final_path` as image_filepaths FROM `db_mould_specification` LEFT JOIN `db_mould_data` ON `db_mould_specification`.`mould_id` = `db_mould_data`.`mould_dataid` LEFT JOIN `db_employee` AS `db_projecter` ON `db_mould_specification`.`projecter` = `db_projecter`.`employeeid` LEFT JOIN `db_employee` AS `db_designer` ON `db_designer`.`employeeid` = `db_mould_specification`.`designer` LEFT JOIN `db_design_plan` ON `db_mould_specification`.`mould_specification_id` = `db_design_plan`.`specification_id` LEFT JOIN `db_employee` AS `db_drawer` ON `db_drawer`.`employeeid` = `db_design_plan`.`drawer_2d` LEFT JOIN `db_employee` AS `db_design_group` ON `db_design_group`.`employeeid`= `db_design_plan`.`design_group` WHERE `db_mould_specification`.`is_approval` = '1' AND `db_design_plan`.`designid` IN($designid)";
	
	$result = $db->query($sql);
	if($result->num_rows){
		$i = $j = 5;
		while($row = $result->fetch_assoc()){
			$objWorksheet->mergeCells('A'.$i.':A'.($i+1));
			$objWorksheet->mergeCells('B'.$i.':B'.($i+1));
			$objWorksheet->mergeCells('C'.$i.':C'.($i+1));
			$objWorksheet->mergeCells('D'.$i.':D'.($i+1));
			$objWorksheet->mergeCells('E'.$i.':E'.($i+1));
			$objWorksheet->mergeCells('F'.$i.':F'.($i+1));
			$objWorksheet->mergeCells('G'.$i.':G'.($i+1));
			$objWorksheet->mergeCells('H'.$i.':H'.($i+1));
			$objWorksheet->mergeCells('I'.$i.':I'.($i+1));
			$objWorksheet->mergeCells('J'.$i.':J'.($i+1));
			$objWorksheet->mergeCells('K'.$i.':K'.($i+1));
			$objWorksheet->mergeCells('L'.$i.':L'.($i+1));
			$objWorksheet->mergeCells('M'.$i.':M'.($i+1));
			$objWorksheet->mergeCells('N'.$i.':N'.($i+1));
			$objWorksheet->mergeCells('O'.$i.':O'.($i+1));
			$objWorksheet->getCell('A'.$i)->setValue($j-3);
			$objWorksheet->getCell('B'.$i)->setValue($row['customer_code']);
			$objWorksheet->getCell('C'.$i)->setValue($row['project_name']);
			$objWorksheet->getCell('D'.$i)->setValue($row['mould_no']);
			$objWorksheet->getCell('E'.$i)->setValue($row['mould_name']);
			$objWorksheet->getCell('F'.$i)->setValue($row['material_other']);
			$objWorksheet->getCell('G'.$i)->setValue($row['cavity_num']);
			$objWorksheet->getCell('H'.$i)->setValue($row['shrink']);
			$objWorksheet->getCell('I'.$i)->setValue($row['projecter']);
			$objWorksheet->getCell('J'.$i)->setValue($row['designer']);
			$objWorksheet->getCell('K'.$i)->setValue($row['drawer_2d']);
			$objWorksheet->getCell('L'.$i)->setValue($row['design_group']);
			$objWorksheet->getCell('M'.$i)->setValue($row['first_degree']);
			$objWorksheet->getCell('N'.$i)->setValue($row['final_confirm']);
			$objWorksheet->getCell('O'.$i)->setValue($row['t0_time']);
			$objWorksheet->getCell('P'.$i)->setValue('计划');
			$objWorksheet->getCell('P'.($i+1))->setValue('实际');
			foreach($array_design_plan_excel as $key=>$value){ 
	          $plan_k = 'plan_'.$value;
	          $real_k = 'real_'.$value;
				$objWorksheet->getCell($key.$i)->setValue($row[$plan_k]);
				$objWorksheet->getCell($key.($i+1))->setValue($row[$real_k]);
			}
			$j++;
			$i = $i+2;
		}
	
	$j = $i + 1;
    	//$objWorksheet->mergeCells('A'.$i.':G'.$i);
	
	  $objWorksheet->getStyle('A1:AJ'.($i-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
	  $objWorksheet->getStyle('A1:AJ'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
	  $objWorksheet->getStyle('A1:AJ'.($i-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
	  $objWorksheet->getStyle('A1:AJ'.($i-1))->getAlignment()->setWrapText(TRUE);

	
	//设置字体   
	$objStyle1 = $objWorksheet->getStyle('B1:AJ'.($i)); 
	$objFont1 = $objStyle1->getFont();   
	$objFont1->setName('微软雅黑','宋体');  
	$objFont1->setSize(10);   
	$objFont1->setBold(false);   
	$objFont1->getColor()->setARGB('FF000000');  
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
	 
	$filename = "设计计划_".date('Y-m-j_H_i_s').".xls";
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