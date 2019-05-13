<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = $_GET['action'];
if($action == 'mould_excel'){
	$mould_dataid = $_GET['id'];
	//通过id查询报价信息
	$sql = "SELECT * FROM `db_mould_data` WHERE `mould_dataid` = '$mould_dataid'";
	$res = $db->query($sql);
	if($res->num_rows){
		$row = $res->fetch_assoc();
	}

//从网络上获取图片
function http_get_data($url) {  
      
    $ch = curl_init ();  
    curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );  
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );  
    curl_setopt ( $ch, CURLOPT_URL, $url );  
    ob_start ();  
    curl_exec ( $ch );  
    $return_content = ob_get_contents ();  
    ob_end_clean ();  
      
    $return_code = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );  
    return $return_content;  
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
//合并单元格
$objPHPExcel->getActiveSheet()->mergeCells('A1:E5');
$objPHPExcel->getActiveSheet()->mergeCells('F1:N2');
$objPHPExcel->getActiveSheet()->mergeCells('F3:N5');

$objPHPExcel->getActiveSheet()->mergeCells('A6:E6');
$objPHPExcel->getActiveSheet()->mergeCells('F6:G6');
$objPHPExcel->getActiveSheet()->mergeCells('H6:L11');
$objPHPExcel->getActiveSheet()->mergeCells('M6:N6');
$objPHPExcel->getActiveSheet()->mergeCells('O6:p6');

$objPHPExcel->getActiveSheet()->mergeCells('A7:E7');
$objPHPExcel->getActiveSheet()->mergeCells('F7:G7');
	
//设置单元格的值
$objPHPExcel->getActiveSheet()->setCellValue('F1', '嘉泰隆 模具费用分解表');
$objPHPExcel->getActiveSheet()->setCellValue('F3', 'JOTYLONG Tooling Cost Break Down');
$objPHPExcel->getActiveSheet()->setCellValue('O1', '客户名称/Customer');
$objPHPExcel->getActiveSheet()->setCellValue('O2', '项目名称/Program');
$objPHPExcel->getActiveSheet()->setCellValue('O3', '联系人/Attention');
$objPHPExcel->getActiveSheet()->setCellValue('O4', '电话/TEL');
$objPHPExcel->getActiveSheet()->setCellValue('O5', '信箱/E-mail');

$objPHPExcel->getActiveSheet()->setCellValue('P1', $row['client_name']);
$objPHPExcel->getActiveSheet()->setCellValue('P2', $row['project_name']);
$objPHPExcel->getActiveSheet()->setCellValue('P3', $row['contacts']);
$objPHPExcel->getActiveSheet()->setCellValue('P4', $row['tel']);
$objPHPExcel->getActiveSheet()->setCellValue('P5', $row['email']);
$objPHPExcel->getActiveSheet()->setCellValue('A6', '模具名称/Mold Specification');
$objPHPExcel->getActiveSheet()->setCellValue('F6', '型腔数量/Cav. Number');
$objPHPExcel->getActiveSheet()->setCellValue('M6', '首次试模时间/T1 Time');
$objPHPExcel->getActiveSheet()->setCellValue('O6', '最终交付时间/Lead Timeme');

$objPHPExcel->getActiveSheet()->setCellValue('A7', $row['mould_name']);
//获取型腔数量
  
if(strpos($row['cavity_type'],'$$')){
		$cavity_nums = str_replace('$$','+',$row['cavity_type']);
	} else {
		$cavity_nums = '1*'.$row['cavity_type'];
	}
$objPHPExcel->getActiveSheet()->setCellValue('F7', $row['k_num'].'k |'.$cavity_nums);

//设置单元格的文本格式
$objPHPExcel->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);


//添加图片
/*实例化插入图片类*/
$objDrawing = new PHPExcel_Worksheet_Drawing();
/*设置图片路径 切记：只能是本地图片*/
$objDrawing->setPath('../jtl.png');
/*设置图片高度*/
$objDrawing->setWidth(100);
$objDrawing->setHeight(50);
$img_height[] = $objDrawing->getHeight();
/*设置图片要插入的单元格*/
$objDrawing->setCoordinates('A1');
/*设置图片所在单元格的格式*/
$objDrawing->setOffsetX(500);
$objDrawing->setOffsetY(10);
$objDrawing->setRotation(0);
$objDrawing->getShadow()->setVisible(true);
$objDrawing->getShadow()->setDirection(50);
$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());

//设置时间格式
$now_time = date('Ymd',time());
// 输出Excel表格到浏览器下载
 ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$now_time.'.xls"');
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
}


?>