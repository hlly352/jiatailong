<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$reviewid = $_GET['reviewid'];
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
$objPHPexcel = PHPExcel_IOFactory::load('../template_file/design_review.xls');
$objWorksheet = $objPHPexcel->getActiveSheet();
$objPHPexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
//查询模具信息
$sql = "SELECT *,`db_mould_specification`.`cavity_num`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`mould_name`,`db_design_review`.`surface_require`,`db_design_review`.`projecter`,`db_design_review`.`designer`,`db_design_review`.`mould_coefficient` FROM `db_mould_specification` LEFT JOIN `db_design_review` ON `db_mould_specification`.`mould_specification_id` = `db_design_review`.`specification_id` LEFT JOIN `db_employee` AS `db_projecter` ON `db_projecter`.`employeeid` = `db_design_review`.`projecter` LEFT JOIN `db_employee` AS `db_designer` ON `db_designer`.`employeeid` = `db_design_review`.`designer` WHERE `db_design_review`.`reviewid` = '$reviewid'";
	$result = $db->query($sql);
	if($result->num_rows){
			$row = $result->fetch_assoc();
			$objWorksheet->getCell('B2')->setValue($row['mould_no']);
			$objWorksheet->getCell('D2')->setValue($row['project_name']);
			$objWorksheet->getCell('F2')->setValue($row['mould_name']);
			$objWorksheet->getCell('H2')->setValue($row['mould_coefficient']);
			$objWorksheet->getCell('B3')->setValue($row['cavity_num']);
			$objWorksheet->getCell('D3')->setValue($row['surface_require']);
			$objWorksheet->getCell('F3')->setValue($row['designer']);
			$objWorksheet->getCell('H3')->setValue($row['projecter']);
			foreach($array_design_review as $k=>$content){
				$objWorksheet->getCell('C'.($k+5))->setValue($row[$content]);
				$key = $content.'_path';
				$path = $row[$key];
				if($path){
					$real_path = str_replace('..',$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'],$path);
					$real_path = trim($real_path);
		  			//获取图片到本地
					 $return_content = http_get_data($real_path);
					 //把图片写入文件
					$handle = fopen('pic'.$key.'.jpg','w');
			 		fwrite($handle,$return_content);
					fclose($handle);
					/*实例化插入图片类*/
					$objDrawing[$key] = new PHPExcel_Worksheet_Drawing();
					/*设置图片路径 切记：只能是本地图片*/
					$objDrawing[$key]->setPath('./pic'.$key.'.jpg');
					/*设置图片高度*/
					$objDrawing[$key]->setResizeProportional(false);
					$objDrawing[$key]->setWidth(140);
					$objDrawing[$key]->setHeight(65);
					//$img_height[] = $objDrawing[$key]->getHeight();
					/*设置图片要插入的单元格*/
					$objDrawing[$key]->setCoordinates('G'.($k+5));
	
			/*设置图片所在单元格的格式*/
			$objDrawing[$key]->setOffsetX(3);
			$objDrawing[$key]->setOffsetY(3);
			$objDrawing[$key]->setRotation(0);
			$objDrawing[$key]->getShadow()->setVisible(true);
			$objDrawing[$key]->getShadow()->setDirection(50);
			$objDrawing[$key]->setWorksheet($objPHPexcel->getActiveSheet());
	  		}
	  	}
			  
	
	
    	//$objWorksheet->mergeCells('A'.$i.':G'.$i);
	
	  $objWorksheet->getStyle('A1:H14')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
	  // $objWorksheet->getStyle('A1:H9')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
	  $objWorksheet->getStyle('A1:H14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
	  $objWorksheet->getStyle('A1:H14')->getAlignment()->setWrapText(TRUE);
	//设置字体   
	// $objStyle1 = $objWorksheet->getStyle('B1:H14'); 
	// $objFont1 = $objStyle1->getFont();   
	// $objFont1->setName('微软雅黑','宋体');  
	// $objFont1->setSize(10);   
	// $objFont1->setBold(false);   
	// $objFont1->getColor()->setARGB('FF000000');  
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
	 
	$filename = "模具设计评审记录表_".date('Y-m-j_H_i_s').".xls";
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
}else{
	echo 'No data!';
}
?>