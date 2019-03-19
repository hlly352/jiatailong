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
$objActSheet->setTitle('物料入库记录'.date('Y-m-j')."-BY_JTL"); 
$objExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(25);
$objExcel->getActiveSheet()->getStyle('A1:Q1')->getAlignment()->setWrapText(true);
 
//由PHPExcel根据传入内容自动判断单元格内容类型   
$objActSheet->setCellValue('A1', '序号');
$objActSheet->setCellValue('B1', '合同号');
$objActSheet->setCellValue('C1', '模具编号');
$objActSheet->setCellValue('D1', '物料名称');
$objActSheet->setCellValue('E1', '规格');
$objActSheet->setCellValue('F1', '材质');
$objActSheet->setCellValue('G1', '表单号');
$objActSheet->setCellValue('H1', '订单数量');
$objActSheet->setCellValue('I1', '单位');
$objActSheet->setCellValue('J1', '实际数量');
$objActSheet->setCellValue('K1', '单位');
$objActSheet->setCellValue('L1', '单价(含税)');
$objActSheet->setCellValue('M1', '金额(含税)');
$objActSheet->setCellValue('N1', '加工费');
$objActSheet->setCellValue('O1', '供应商');
$objActSheet->setCellValue('P1', '入库日期');
$objActSheet->setCellValue('Q1', '对账');

$sql = $_SESSION['material_inout_list_in']." ORDER BY `db_material_inout`.`inoutid` DESC";
$result = $db->query($sql);
if($result->num_rows){
	$i = 2;
	while($row = $result->fetch_assoc()){
		$check_status = $row['check_status']?'已对账':'未对账';
		$objActSheet->setCellValue('A'.$i, $i-1);
		$objActSheet->setCellValue('B'.$i, $row['order_number']);
		$objActSheet->setCellValue('C'.$i, $row['mould_number']);
		$objActSheet->setCellValue('D'.$i, $row['material_name']);
		$objActSheet->setCellValue('E'.$i, $row['specification']);
		$objActSheet->setCellValue('F'.$i, $row['texture']);
		$objActSheet->setCellValue('G'.$i, $row['form_number']);
		$objActSheet->setCellValue('H'.$i, $row['quantity']);
		$objActSheet->setCellValue('I'.$i, $row['unit_name_order']);
		$objActSheet->setCellValue('J'.$i, $row['inout_quantity']);
		$objActSheet->setCellValue('K'.$i, $row['unit_name_actual']);
		$objActSheet->setCellValue('L'.$i, $row['unit_price']);
		$objActSheet->setCellValue('M'.$i, $row['amount']);
		$objActSheet->setCellValue('N'.$i, $row['process_cost']);
		$objActSheet->setCellValue('O'.$i, $row['supplier_cname']);
		$objActSheet->setCellValue('P'.$i, $row['dodate']);
		$objActSheet->setCellValue('Q'.$i, $check_status);
		$i++;
	}

//格式
$objActSheet->getStyle('A1:Q'.($i-1))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
$objActSheet->getStyle('A1:Q'.($i-1))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
$objActSheet->getStyle('A1:Q'.($i-1))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
		
//设置宽度
$objActSheet->getColumnDimension('A')->setWidth(5);  
$objActSheet->getColumnDimension('B')->setWidth(15);
$objActSheet->getColumnDimension('C')->setWidth(15);
$objActSheet->getColumnDimension('D')->setWidth(18);
$objActSheet->getColumnDimension('E')->setWidth(25);
$objActSheet->getColumnDimension('F')->setWidth(15);
$objActSheet->getColumnDimension('G')->setWidth(15);
$objActSheet->getColumnDimension('O')->setWidth(15);
$objActSheet->getColumnDimension('P')->setWidth(15);
$objActSheet->getColumnDimension('Q')->setWidth(12);

//设置字体   
$objStyle2 = $objActSheet->getStyle('A1:Q1'); 
$objFont2 = $objStyle2->getFont();   
$objFont2->setName('微软雅黑','宋体');   
$objFont2->setSize(10);
$objFont2->setBold(true);   
$objFont2->getColor()->setARGB('FF000000'); 
//设置字体   
$objStyle1 = $objActSheet->getStyle('A2:Q'.($i)); 
$objFont1 = $objStyle1->getFont();   
$objFont1->setName('微软雅黑','宋体');   
$objFont1->setSize(10);   
$objFont1->setBold(false);   
$objFont1->getColor()->setARGB('FF000000'); 

//输出内容   
//$m_strOutputPath = "../upload/tmp/"; 
$m_strOutputExcelFileName = '物料入库记录_'.date('Y-m-j_H_i_s').".xls";
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