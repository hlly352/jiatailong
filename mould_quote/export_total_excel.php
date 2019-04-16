<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$id = $_GET['id'];
$ids = implode(',',$id);

//通过id查询报价单数据
$sql = 'SELECT * FROM `db_mould_data` WHERE `mould_dataid` IN ('.$ids.')';
$result = $db->query($sql);
$row = [];
while($info = $result->fetch_assoc()){
	$row[] = $info;
}

/** Error reporting */
error_reporting(E_ALL);
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../class/');
/** PHPExcel */
include 'PHPExcel.php';
$objPHPExcel = new PHPExcel();
//设置当前的sheet
$objPHPExcel->setActiveSheetIndex(0);
//设置sheet的name
$objPHPExcel->getActiveSheet()->settitle('Simple');
//设置单元格的值
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'String');
$objPHPExcel->getActiveSheet()->setCellValue('F1', 'JOTYLONG TOLLING');
$objPHPExcel->getActiveSheet()->setCellValue('F3','Quotation');
$objPHPExcel->getActiveSheet()->setCellValue('P3','No:JTL');
$objPHPExcel->getActiveSheet()->setCellValue('A4', 'To:客户名称');
$objPHPExcel->getActiveSheet()->setCellValue('A5', '联系人');
$objPHPExcel->getActiveSheet()->setCellValue('A6', '项目名称');
$objPHPExcel->getActiveSheet()->setCellValue('P4', 'Suzhou JoTyLong Industrial Co.,Ltd');
$objPHPExcel->getActiveSheet()->setCellValue('P5', 'From:');
$objPHPExcel->getActiveSheet()->setCellValue('P6','Date:');
$objPHPExcel->getActiveSheet()->setCellValue('A7','Item');
$objPHPExcel->getActiveSheet()->setCellValue('B7','Part Name/Number');
$objPHPExcel->getActiveSheet()->setCellValue('C7','Part Picture');
$objPHPExcel->getActiveSheet()->setCellValue('D7','Part Size');
$objPHPExcel->getActiveSheet()->setCellValue('E7','Plastic Material');
$objPHPExcel->getActiveSheet()->setCellValue('F7','No. of Cavities');
$objPHPExcel->getActiveSheet()->setCellValue('G7','Mould Type');
$objPHPExcel->getActiveSheet()->setCellValue('H7','Mould Size');
$objPHPExcel->getActiveSheet()->setCellValue('I7','Mould weight');
$objPHPExcel->getActiveSheet()->setCellValue('J7','Mould Base steel');
$objPHPExcel->getActiveSheet()->setCellValue('K7','Cavity and Core Sheel');
$objPHPExcel->getActiveSheet()->setCellValue('L7','Runner and Gate Type');
$objPHPExcel->getActiveSheet()->setCellValue('M7','T1 Lead Time(Days)');
$objPHPExcel->getActiveSheet()->setCellValue('N7','Quantity(Sets)');
$objPHPExcel->getActiveSheet()->setCellValue('O7','Hot Runner Price');
$objPHPExcel->getActiveSheet()->setCellValue('P7','Mould Price');
$objPHPExcel->getActiveSheet()->setCellValue('Q7','Total Price(RMB) including 17% Tax');
$objPHPExcel->getActiveSheet()->setCellValue('R7','Remarks');
foreach($row as $key=>$val){
	$keys = $key +8;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$keys,$key+1);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$keys,$val['mould_name']);
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$keys,'图片');
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$keys,'Part Size');
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$keys,$val['m_material']);
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$keys,'No. of Cavities');
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$keys,'2P');
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$keys,'Mould Size');
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$keys,$val['m_weight']);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$keys,'Mould Base steel');
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$keys,'Cavity and Core Sheel');
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$keys,'Runner and Gate Type');
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$keys,$val['t_time']);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$keys,'1');
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$keys,'Hot Runner Price');
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$keys,'Mould Price');
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$keys,$val['mold_with_vat']);
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$keys,'Remarks');
	$num = $keys + 1;
}
//设置总计
$objPHPExcel->getActiveSheet()->setCellValue('A'.$num,'Total');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$num.':M'.$num);
//合并单元格
$objPHPExcel->getActiveSheet()->mergeCells('A1:B3');
$objPHPExcel->getActiveSheet()->mergeCells('F1:L2');
$objPHPExcel->getActiveSheet()->mergeCells('F3:L3');
$objPHPExcel->getActiveSheet()->mergeCells('P3:R3');
$objPHPExcel->getActiveSheet()->mergeCells('P4:R4');
$objPHPExcel->getActiveSheet()->mergeCells('P5:R5');
$objPHPExcel->getActiveSheet()->mergeCells('P6:R6');
$objPHPExcel->getActiveSheet()->mergeCells('A4:D4');
$objPHPExcel->getActiveSheet()->mergeCells('A5:D5');
$objPHPExcel->getActiveSheet()->mergeCells('A6:D6');
//设置单元格水平格式
$objPHPExcel->getActiveSheet()->getstyle('A1')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getstyle('F1')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getstyle('F3')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getstyle('P3')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getstyle('P4')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getstyle('P5')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_RIGHT);
$objPHPExcel->getActiveSheet()->getstyle('P6')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_RIGHT);

//设置单元格垂直格式
$arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R');

foreach($arr as $k=>$v){
	for($i = 1;$i<=$num;$i++){
		$objPHPExcel->getActiveSheet()->getstyle($v.$i)->getAlignment()->setVertical(PHPExcel_style_Alignment::VERTICAL_CENTER);

		if($i>6){
			for($k=7;$k<$num+1;$k++){
			$objPHPExcel->getActiveSheet()->getstyle($v.$k)->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_CENTER);	
				}
			}
		}
}

//设置宽度
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(12);
//计算单元格的和
$objPHPExcel->getActiveSheet()->setCellValue('N'.$num ,'=SUM(N8:N'.$keys.')');
$objPHPExcel->getActiveSheet()->setCellValue('P'.$num ,'=SUM(P8:P'.$keys.')');
$objPHPExcel->getActiveSheet()->setCellValue('O'.$num ,'=SUM(O8:O'.$keys.')');
$objPHPExcel->getActiveSheet()->setCellValue('Q'.$num ,'=SUM(Q8:Q'.$keys.')');
//设置边框
//$objPHPExcel->getActiveSheet()->getstyle('F1')->getBorders()->getBottom()->getColor()->setARGB('000000');
   // $objPHPExcel->getActiveSheet()->getStyle('A1:A2')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
 $objPHPExcel->getActiveSheet()->getStyle('A1:B3')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
// 输出Excel表格到浏览器下载
 ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="abc.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
$objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
$objWriter->save('php://output'); 


/*$objPHPexcel = PHPExcel_IOFactory::load('../template_file/cutter_apply.xls');
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
$objWriter->save('php://output');*/
?>