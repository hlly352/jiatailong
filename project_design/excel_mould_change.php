<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$changeid = $_GET['changeid'];
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
$objPHPexcel = PHPExcel_IOFactory::load('../template_file/mould_change.xls');
$objWorksheet = $objPHPexcel->getActiveSheet();
$objPHPexcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
//查询模具信息
$sql = "SELECT `db_mould_change`.`geter`,`db_mould_change`.`document_use`,`db_mould_change`.`document_location`,`db_mould_change`.`special_require`,`db_mould_change`.`document_no`,`db_mould_change`.`image_path`,`db_mould_specification`.`project_name`,`db_mould_specification`.`mould_no`,`db_mould_specification`.`mould_name`,`db_mould_specification`.`customer_code`,`db_designer`.`employee_name` AS `designer`,`db_engnieer`.`employee_name` AS `engnieer`,`db_approval`.`employee_name` AS `approval`,`db_check`.`employee_name` AS `check`,`db_mould_change`.`data_content`,`db_mould_change`.`data_dept`,`db_mould_change`.`change_parts`,`db_mould_change`.`cancel_parts` FROM `db_mould_change` INNER JOIN `db_mould_specification` ON `db_mould_specification`.`mould_specification_id` = `db_mould_change`.`specification_id` LEFT JOIN `db_employee` AS `db_designer` ON `db_designer`.`employeeid` = `db_mould_change`.`designer` LEFT JOIN `db_employee` AS `db_engnieer` ON `db_engnieer`.`employeeid` = `db_mould_change`.`engnieer` LEFT JOIN `db_employee` AS `db_approval` ON `db_approval`.`employeeid` = `db_mould_change`.`approval` LEFT JOIN `db_employee` AS `db_check` ON `db_check`.`employeeid` = `db_mould_change`.`check` WHERE `db_mould_change`.`changeid` = '$changeid'";

	$result = $db->query($sql);
	if($result->num_rows){
			$row = $result->fetch_assoc();
			//签收部门
			$get_dept = '';
			foreach($array_data_dept as $v){
				$get_dept .= $v.':       ';
			}
			//文档用途
			$array_use = explode('&&',$row['document_use']);
			$array_document_use = array('K'=>'开粗','J'=>'精光','A'=>'按特殊要求:');
			$uses = '';
			if($row['document_use']){
				foreach($array_use as $v){
					$uses .= $array_document_use[$v].' ';
				}
			}
			if(in_array('A',$array_use)){
				$uses .= $row['special_require'];
			}
			 //查询接收人员
			 $geter = $row['geter'];
			 $sql_employee = "SELECT `deptid`,GROUP_CONCAT(`employee_name`) AS `geter` FROM `db_employee` WHERE `employeeid` IN($geter) GROUP BY `deptid`";
             $geter_name = '';
             if($geter){
				 $result_employee = $db->query($sql_employee);
				 if($result_employee->num_rows){
	            	while($row_employee = $result_employee->fetch_assoc()){
	              	  $geter_name .= $array_data_dept[$row_employee['deptid']].'('.$row_employee['geter'].')  ';
	            	}
	          	 }
	          	}
			//获取资料内容和接收部门
			$contents = '';
			if($row['data_content']){
				$array_content = array();
				if(stripos($row['data_content'],'&&')){
					$array_content = explode('&&',$row['data_content']);
				}else{
					$array_content[] = $row['data_content'];
				}
				foreach($array_content as $v){
					$contents .= $array_data_content[$v].' ';
				}
			}
			$depts = '';
			if($row['data_dept']){
				$array_dept = array();
				if(stripos($row['data_dept'],'&&')){
					$array_dept = explode('&&',$row['data_dept']);
				}else{
					$array_dept[] = $row['data_dept'];
				}
				foreach($array_dept as $v){
					$depts .= $array_data_dept[$v].' ';
				}
			}
			$objWorksheet->getCell('F2')->setValue('文件编号:');
			$objWorksheet->getCell('G2')->setValue($row['document_no']);
			$objWorksheet->getCell('B3')->setValue($row['customer_code']);
			$objWorksheet->getCell('D3')->setValue($row['project_name']);
			$objWorksheet->getCell('F3')->setValue($row['mould_no']);
			$objWorksheet->getCell('H3')->setValue($row['mould_name']);
			$objWorksheet->getCell('B4')->setValue($contents);
			$objWorksheet->getCell('B5')->setValue($row['change_parts']);
			$objWorksheet->getCell('F5')->setValue($row['cancel_parts']);
	
    	//$objWorksheet->mergeCells('A'.$i.':G'.$i);
	
	  $objWorksheet->getStyle('A3:H5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN); //设置单元格为实线
	  $objWorksheet->getStyle('A1:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);  //水平居中
	  $objWorksheet->getStyle('A5:A5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	  $objWorksheet->getStyle('B4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
	  $objWorksheet->getStyle('A1:H9')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);// 竖直居中
	 	// $objWorksheet->getStyle('A1:H9')->getAlignment()->setWrapText(TRUE);
	  //获取图片
	  $image_path = trim($row['image_path']);
	  $array_path = array();
	  if(!empty($image_path)){
	  	if(stripos($image_path,'$')){
	  		$array_path = explode('$',$image_path);
	  	}else{
	  		$array_path[] = $image_path;
	  	}
	  	foreach($array_path as $key=>$path){
	  		$imgs = explode('##',$path);
	  		if($imgs[0]){
				$real_path = str_replace('..',$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'],$imgs[0]);
				$remark = $imgs[1];
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
			$objDrawing[$key]->setWidth(300);
			$objDrawing[$key]->setHeight(200);
			//$img_height[] = $objDrawing[$key]->getHeight();
			/*设置图片要插入的单元格*/
			switch ($key%2) {
				case '0':
					$objDrawing[$key]->setCoordinates('A'.(7+$key*5));
					$objWorksheet->getCell('B'.(15+$key*5))->setValue($remark);
					break;
				case '1':
					$objDrawing[$key]->setCoordinates('E'.(($key-1)*5+7));
					$objWorksheet->getCell('F'.(($key-1)*5+15))->setValue($remark);
					break;
			}
			
			/*设置图片所在单元格的格式*/
			$objDrawing[$key]->setOffsetX(25);
			$objDrawing[$key]->setOffsetY(50);
			$objDrawing[$key]->setRotation(0);
			$objDrawing[$key]->getShadow()->setVisible(true);
			$objDrawing[$key]->getShadow()->setDirection(50);
			$objDrawing[$key]->setWorksheet($objPHPexcel->getActiveSheet());
	  		}
	  	}
	}
	
	$i = ceil((count($array_path) - 1)/2);
	$j = $i*10+7;
	$objWorksheet->getCell('A'.$j)->setValue('以上所有图档');
	$objWorksheet->getCell('B'.$j)->setValue($uses);
	$objWorksheet->getCell('A'.($j+1))->setValue('图档位置');
	$objWorksheet->getCell('B'.($j+1))->setValue($row['document_location']);
	$objWorksheet->getCell('A'.($j+2))->setValue('原图设计师');
	$objWorksheet->getCell('B'.($j+2))->setValue($row['designer']);
	$objWorksheet->getCell('C'.($j+2))->setValue('更改工程师');
	$objWorksheet->getCell('D'.($j+2))->setValue($row['engnieer']);
	$objWorksheet->getCell('E'.($j+2))->setValue('审核');
	$objWorksheet->getCell('F'.($j+2))->setValue($row['check']);
	$objWorksheet->getCell('G'.($j+2))->setValue('批准');
	$objWorksheet->getCell('H'.($j+2))->setValue($row['approval']);
	$objWorksheet->getCell('A'.($j+3))->setValue('接收部门');
	$objWorksheet->getCell('B'.($j+3))->setValue($geter_name);
	$objWorksheet->getCell('A'.($j+4))->setValue('签收部门');
	$objWorksheet->getCell('B'.($j+4))->setValue($get_dept);
	//合并单元格
	$objWorksheet->mergeCells('B'.$j.':H'.$j);
	$objWorksheet->mergeCells('B'.($j+1).':H'.($j+1));
	$objWorksheet->mergeCells('B'.($j+3).':H'.($j+3));
	$objWorksheet->mergeCells('B'.($j+4).':H'.($j+4));
	//设置边框线
	$objWorksheet->getStyle('A3'.':A'.($j+4))->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objWorksheet->getStyle('H3'.':H'.($j+4))->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	$objWorksheet->getStyle('A'.$j.':H'.($j+4))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
	//设置字体   
	$objStyle1 = $objWorksheet->getStyle('A2:H9'); 
	$objFont1 = $objStyle1->getFont();   
	$objFont1->setName('微软雅黑','宋体');  
	$objFont1->setSize(10);   
	$objFont1->setBold(false);   
	$objFont1->getColor()->setARGB('FF000000');  
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
	 
	$filename = "模具更改联络单_".date('Y-m-j_H_i_s').".xls";
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