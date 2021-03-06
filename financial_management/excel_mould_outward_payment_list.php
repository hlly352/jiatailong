<?php
require_once '../global_mysql_connect.php';
require_once 'shell.php';
/** Error reporting */
error_reporting(E_ALL);
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../class/');
/** PHPExcel */
include 'PHPExcel.php';
// uncomment   
require_once 'PHPExcel/Writer/Excel5.php';    // 用于其他低版本xls   
// or   
//require_once 'PHPExcel/Writer/Excel2007.php'; // 用于 excel-2007 格式

// Create new PHPExcel object
$objExcel = new PHPExcel();
// 创建文件格式写入对象实例, uncomment   
$objWriter = new PHPExcel_Writer_Excel5($objExcel);    // 用于其他版本格式   
// or   
//$objWriter = new PHPExcel_Writer_Excel2007($objExcel); // 用于 2007 格式   
//$objWriter->setOffice2003Compatibility(true);   

//设置文档基本属性   
$objProps = $objExcel->getProperties();   
$objProps->setCreator("jtl");   
$objProps->setLastModifiedBy("Zeal Li");   
$objProps->setTitle("Office XLS Test Document");   
$objProps->setSubject("Office XLS Test Document, Demo");   
$objProps->setDescription("Test document, generated by PHPExcel.");   
$objProps->setKeywords("office excel PHPExcel");   
$objProps->setCategory("jtl");  

//设置当前的sheet索引，用于后续的内容操作。   
//一般只有在使用多个sheet的时候才需要显示调用。   
//缺省情况下，PHPExcel会自动创建第一个sheet被设置SheetIndex=0   
$objExcel->setActiveSheetIndex(0);
$objActSheet = $objExcel->getActiveSheet();
$objActSheet->setTitle('外协加工付款记录'.date('Y-m-j')."-BY_JTL"); 
$objExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
$objExcel->getActiveSheet()->getStyle('A1:M11')->getAlignment()->setWrapText(true);
 
//由PHPExcel根据传入内容自动判断单元格内容类型   
$objActSheet->setCellValue('A1', '序号');
$objActSheet->setCellValue('B1', '模具编号');
$objActSheet->setCellValue('C1', '零件编号');
$objActSheet->setCellValue('D1', '外协时间');
$objActSheet->setCellValue('E1', '申请组别');
$objActSheet->setCellValue('F1', '外协单号');
$objActSheet->setCellValue('G1', '数量');
$objActSheet->setCellValue('H1', '供应商');
$objActSheet->setCellValue('I1', '类型');
$objActSheet->setCellValue('J1', '金额');
$objActSheet->setCellValue('K1', '已付现金');
$objActSheet->setCellValue('L1', '付款日期');
$objActSheet->setCellValue('M1', '付款人');

$sql = $_SESSION['mould_outward_payment_list']." ORDER BY `db_cash_pay`.`pay_date` DESC,`db_cash_pay`.`payid` DESC";
$result = $db->query($sql);
if($result->num_rows){
	$i = 2;
	while($row = $result->fetch_assoc()){
		$mould_number = $row['mouldid']?$row['mould_number']:'--';
		$objActSheet->setCellValue('A'.$i, $i-1);
		$objActSheet->setCellValue('B'.$i, $mould_number);
		$objActSheet->setCellValue('C'.$i, $row['part_number']);
		$objActSheet->setCellValue('D'.$i, $row['order_date']);
		$objActSheet->setCellValue('E'.$i, $row['workteam_name']);
		$objActSheet->setCellValue('F'.$i, $row['order_number']);
		$objActSheet->setCellValue('G'.$i, $row['quantity']);
		$objActSheet->setCellValue('H'.$i, $row['supplier_cname']);
		$objActSheet->setCellValue('I'.$i, $row['outward_typename']);
		$objActSheet->setCellValue('J'.$i, $row['cost']);
		$objActSheet->setCellValue('K'.$i, $row['pay_amount']);
		$objActSheet->setCellValue('L'.$i, $row['pay_date']);
		$objActSheet->setCellValue('M'.$i, $row['employee_name']);
		$i++;
	}

//格式
$objActSheet->getStyle('A1:M'.($i-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
$objActSheet->getStyle('A1:M'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
$objActSheet->getStyle('A1:M'.($i-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
		
//设置宽度
$objActSheet->getColumnDimension('A')->setWidth(5);  
$objActSheet->getColumnDimension('B')->setWidth(15);
$objActSheet->getColumnDimension('C')->setWidth(30);
$objActSheet->getColumnDimension('D')->setWidth(12);
$objActSheet->getColumnDimension('E')->setWidth(15);
$objActSheet->getColumnDimension('F')->setWidth(15);
$objActSheet->getColumnDimension('G')->setWidth(12);
$objActSheet->getColumnDimension('H')->setWidth(15);
$objActSheet->getColumnDimension('I')->setWidth(12);
$objActSheet->getColumnDimension('J')->setWidth(12);
$objActSheet->getColumnDimension('K')->setWidth(12);
$objActSheet->getColumnDimension('L')->setWidth(12);
$objActSheet->getColumnDimension('M')->setWidth(12);

//设置字体   
$objStyle2 = $objActSheet->getStyle('A1:M1'); 
$objFont2 = $objStyle2->getFont();   
$objFont2->setName('微软雅黑','宋体');   
$objFont2->setSize(10);
$objFont2->setBold(true);   
$objFont2->getColor()->setARGB('FF000000'); 
//设置字体   
$objStyle1 = $objActSheet->getStyle('A2:M'.($i)); 
$objFont1 = $objStyle1->getFont();   
$objFont1->setName('微软雅黑','宋体');   
$objFont1->setSize(10);   
$objFont1->setBold(false);   
$objFont1->getColor()->setARGB('FF000000'); 

//输出内容   
//$m_strOutputPath = "../upload/tmp/"; 
$m_strOutputExcelFileName = '外协加工付款记录_'.date('Y-m-j_H_i_s').".xls";
//输到文件   
//$objWriter->save($m_strOutputPath . $m_strOutputExcelFileName);

//到浏览器
header("Content-Type: application/force-download");   
header("Content-Type: application/octet-stream");   
header("Content-Type: application/download");   
header('Content-Disposition:inline;filename="'.$m_strOutputExcelFileName.'"');   
header("Content-Transfer-Encoding: binary");   
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");   
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");   
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");   
header("Pragma: no-cache");   
$objWriter->save('php://output');
}else{
	echo "No data!";
}
?>