<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$id = $_GET['id'];

if($id == null){
	
	header("location:mould_data_approval.php");
	exit;
}
$ids = implode(',',$id);

//通过id查询报价单数据
$sql = 'SELECT * FROM `db_mould_data` WHERE `mould_dataid` IN ('.$ids.')';
$result = $db->query($sql);
$row = [];
while($info = $result->fetch_assoc()){
	$row[] = $info;
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
foreach($row as $key=>$val){
	//获取图片
	$image_filepath = $val['upload_final_path'];
	  if(stristr($image_filepath,'$') == true){
		  	$image_filepath = substr($image_filepath,0,stripos($image_filepath,"$"));
			}
			$image_path = substr($image_filepath,0,strrpos($image_filepath,'/'));   
			$image_path = str_replace('..','http://localhost',$image_filepath);
			//获取图片到本地
			 $return_content = http_get_data($image_path);  
	//把图片写入文件
	$handle = fopen('pic'.$key.'.jpg','w');
	 fwrite($handle,$return_content);
	fclose($handle);
	/*实例化插入图片类*/
	$objDrawing[$key] = new PHPExcel_Worksheet_Drawing();

	/*设置图片路径 切记：只能是本地图片*/
	$objDrawing[$key]->setPath('./pic'.$key.'.jpg');
	/*设置图片高度*/
	$objDrawing[$key]->setWidth(80);
	$objDrawing[$key]->setHeight(50);
	$img_height[] = $objDrawing[$key]->getHeight();
	/*设置图片要插入的单元格*/
	$pic_num = $key + 8;
	$objDrawing[$key]->setCoordinates('C'.$pic_num);
	/*设置图片所在单元格的格式*/
	$objDrawing[$key]->setOffsetX(20);
	$objDrawing[$key]->setOffsetY(20);
	$objDrawing[$key]->setRotation(0);
	$objDrawing[$key]->getShadow()->setVisible(true);
	$objDrawing[$key]->getShadow()->setDirection(50);
	$objDrawing[$key]->setWorksheet($objPHPExcel->getActiveSheet());
	
//设置单元格的值
$objPHPExcel->getActiveSheet()->setCellValue('A1', ' ');
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
$objPHPExcel->getActiveSheet()->setCellValue('B7','Part Name');
$objPHPExcel->getActiveSheet()->setCellValue('C7','Part Picture');
$objPHPExcel->getActiveSheet()->setCellValue('D7','Part Size');
$objPHPExcel->getActiveSheet()->setCellValue('E7','Plastic Material');
$objPHPExcel->getActiveSheet()->setCellValue('F7','Cav. No.');
$objPHPExcel->getActiveSheet()->setCellValue('G7','Mould Type');
$objPHPExcel->getActiveSheet()->setCellValue('H7','Mould Size');
$objPHPExcel->getActiveSheet()->setCellValue('I7','Mould weight');
$objPHPExcel->getActiveSheet()->setCellValue('J7','Mould Base steel');
$objPHPExcel->getActiveSheet()->setCellValue('K7','Cavity and Core Sheel');
$objPHPExcel->getActiveSheet()->setCellValue('L7','Runner and Gate Type');
$objPHPExcel->getActiveSheet()->setCellValue('M7','T1 Lead Time(Days)');
$objPHPExcel->getActiveSheet()->setCellValue('N7','QTY');
$objPHPExcel->getActiveSheet()->setCellValue('O7','Hot Runner Price');
$objPHPExcel->getActiveSheet()->setCellValue('P7','Mould Price');
$objPHPExcel->getActiveSheet()->setCellValue('Q7','Total Price(RMB) with Tax');
$objPHPExcel->getActiveSheet()->setCellValue('R7','Remarks');
$arr_note = [
	'Notes:',
	'  1)Payment:40% deposit,30% after first sampling,20% after mould release and 10% after 90day’s mould release.',
	'  2)Leadtime(1st shot)As above are based on the confirmation of final drawings and deposit.',
	'  3)Mold Test: Including 3 times testing and 10 shots for each trial shot(Freight of Sample delivery and cost of sampling material should be on customer\'s account)',
	'  4)Prices are subject to mold modifications or drawing changes. If the exchange rate fluctuates more than 5%, the offer will be adjusted accordingly.',
	'  5)Delivery: JoTyLong Ex Works.',
	'  6)Validity of this quotation: 30 days.'
	];

	  //获取零件编号
		  if(substr_count($val['part_number'],'$$') !=0){
		  	$part_number = explode('$$',$val['part_number']);
		  	$p_length = explode('$$',$val['p_length']);
		  	$p_width = explode('$$',$val['p_width']);
		  	$p_height = explode('$$',$val['p_height']);
		  	$m_material = explode('$$',$val['m_material']);
		  	$arr1= arr_merge($p_length,$p_width);
			$arr_res = arr_merge($arr1,$p_height);
			//多个模穴数时拼接尺寸
			$re = ' ';
			for($i=0;$i<count($arr_res);$i++){
				$re .= $arr_res[$i][0]."*".$arr_res[$i][1]."*".$arr_res[$i][2]."\n\r";
				
			}
		
			} else {
			    $part_number = $val['part_number'];	
			    $m_material = $val['m_material'];
			    $p_length = $val['p_length'];
			    $p_width = $val['p_width'];
			    $p_height = $val['p_height'];
			    $re = $p_length.'*'.$p_width.'*'.$p_height;
			}
			
		  //获取模穴数
		  $cavity_num = turn_arr($val['cavity_type']);
	
		  if(count($cavity_num)  == 1){
		  	$cavity_nu = '1*'.$cavity_num[0];
		  } else {
		  	$cavity_nu = $cavity_num[0];
		  	for($i = 1;$i<count($cavity_num);$i++){
		  		
		  			
		  			$cavity_nu .= '+'.$cavity_num[$i];
		  		
		  	}
		  }
		 //获取加工材料费的数据
		  $old_material = [$val['mould_material'],$val['material_specification'],$val['materials_number'],$val['material_length'],$val['material_width'],$val['material_height'],$val['material_weight'],$val['material_unit_price'],$val['material_price']];
		 $arrs_materials = getdata($old_material);
	
		  //获取模具配件的数据
		   $old_standard = [$val['mold_standard'],$val['standard_specification'],$val['standard_supplier'],$val['standard_number'],$val['standard_unit_price'],$val['standard_price']];
		 $arrs_standards = getdata($old_standard);

		 //获取热流道类型
		 if($arrs_standards[4][1] !=0&&$arrs_standards[4][1] != null){
        			$hot_runner =  $arrs_standards[4][2].'/'.$arrs_standards[4][1];
        		} else {
        			$hot_runner = '无';
        		}
        		$hot_price = $val['mold_with_vat'] - $arrs_standards[4][4];
	$keys = $key +8;
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$keys,$key+1);
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$keys,$val['mould_name']);
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$keys,str_replace(PHP_EOL,'',$re));
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$keys,str_replace('$$',"\n\r",$val['m_material']));
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$keys,$cavity_nu);
	$objPHPExcel->getActiveSheet()->setCellValue('G'.$keys,'2P');
	$objPHPExcel->getActiveSheet()->setCellValue('H'.$keys,$val['m_length'].'*'.$val['m_width'].'*'.$val['m_height'] );
	$objPHPExcel->getActiveSheet()->setCellValue('I'.$keys,$val['m_weight']);
	$objPHPExcel->getActiveSheet()->setCellValue('J'.$keys,'Mould Base steel');
	$objPHPExcel->getActiveSheet()->setCellValue('K'.$keys,$arrs_materials[1][1].'/'.$arrs_materials[2][1]);
	$objPHPExcel->getActiveSheet()->setCellValue('L'.$keys,$hot_runner);
	$objPHPExcel->getActiveSheet()->setCellValue('M'.$keys,$val['t_time']);
	$objPHPExcel->getActiveSheet()->setCellValue('N'.$keys,'1');
	$objPHPExcel->getActiveSheet()->setCellValue('O'.$keys,$arrs_standards[4][4]);
	$objPHPExcel->getActiveSheet()->setCellValue('P'.$keys,$hot_price);
	$objPHPExcel->getActiveSheet()->setCellValue('Q'.$keys,$val['mold_with_vat']);
	$objPHPExcel->getActiveSheet()->setCellValue('R'.$keys,' ');
	$num = $keys + 1;

}
//设置总计
$objPHPExcel->getActiveSheet()->setCellValue('A'.$num,'Total');
$objPHPExcel->getActiveSheet()->mergeCells('A'.$num.':M'.$num);
//合并单元格说明文字的格式
for($x = 0;$x<9;$x++){
	$rows = $x+$num+1;
	$row = $rows + 1;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$rows.':R'.$rows);
	//设置说明文字的内容
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$row,$arr_note[$x]);
	}
	$nu = $num + 10;

$objPHPExcel->getActiveSheet()->mergeCells('C'.$nu.':E'.$nu);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$nu.':Q'.$nu);
$objPHPExcel->getActiveSheet()->setCellValue('C'.$nu,'Suzhou JoTyLong Company');
$objPHPExcel->getActiveSheet()->setCellValue('M'.$nu,'Confirmed & Accepted By');
$nus = $nu +2;
$objPHPExcel->getActiveSheet()->mergeCells('C'.$nus.':E'.$nus);
$objPHPExcel->getActiveSheet()->mergeCells('M'.$nus.':Q'.$nus);
$objPHPExcel->getActiveSheet()->setCellValue('C'.$nus,'Authorized Signatures');
$objPHPExcel->getActiveSheet()->setCellValue('M'.$nus,'Please Sign & Return');
$nus = $nus+1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$nus.':R'.$nus);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$nus,'Add: No.1429 Cangzhong Road, Mudu Town,Suzhou City, Jiangsu Province, P.R.China');
$nus = $nus+1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$nus.':R'.$nus);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$nus,'TEL:+86 0512 - 66932311   FAX:+86-512- 6693-3835');
$nus = $nus +1;
$objPHPExcel->getActiveSheet()->mergeCells('A'.$nus.':R'.$nus);
$objPHPExcel->getActiveSheet()->setCellValue('A'.$nus,'Webstie:www.jotylong.com     E-mail: market@jotylong.com');


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
$objPHPExcel->getActiveSheet()->getstyle('C22')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getstyle('M22')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getstyle('C24')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getstyle('M24')->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_CENTER);


//设置单元格垂直格式
$arr = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R');

foreach($arr as $k=>$v){
	for($i = 1;$i<=$num;$i++){
		$objPHPExcel->getActiveSheet()->getstyle($v.$i)->getAlignment()->setVertical(PHPExcel_style_Alignment::VERTICAL_CENTER);

		if($i>6){
			for($k=7;$k<$num+1;$k++){
			//设置单元格水平居中
			$objPHPExcel->getActiveSheet()->getstyle($v.$k)->getAlignment()->setHorizontal(PHPExcel_style_Alignment::HORIZONTAL_CENTER);	
			//内容的高度
			if($k<$num){
				$objPHPExcel->getActiveSheet()->getRowDimension($k)->setRowHeight(60);
					}
				}
			}
		}
}
//设置单元格自动换行
$objPHPExcel->getActiveSheet()->getStyle('A1:R100')->getAlignment()->setWrapText(TRUE);
$m = 6.6;
//设置宽度
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth($m);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth($m*3);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth($m*3);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth($m*2);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth($m*2);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth($m);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth($m);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth($m*2);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth($m);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth($m*2);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth($m*2);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth($m*2);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth($m*2);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth($m);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth($m*2);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth($m*2);
$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth($m*2);
$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth($m*3);
//设置高度
$objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);
$objPHPExcel->getActiveSheet()->getRowDimension('7')->setRowHeight(44);

//计算单元格的和
$objPHPExcel->getActiveSheet()->setCellValue('N'.$num ,'=SUM(N8:N'.$keys.')');
$objPHPExcel->getActiveSheet()->setCellValue('P'.$num ,'=SUM(P8:P'.$keys.')');
$objPHPExcel->getActiveSheet()->setCellValue('O'.$num ,'=SUM(O8:O'.$keys.')');
$objPHPExcel->getActiveSheet()->setCellValue('Q'.$num ,'=SUM(Q8:Q'.$keys.')');
//添加图片
/*实例化插入图片类*/
$objDrawing = new PHPExcel_Worksheet_Drawing();
/*设置图片路径 切记：只能是本地图片*/
$objDrawing->setPath('../jtl.png');
/*设置图片高度*/
$objDrawing->setWidth(120);
$objDrawing->setHeight(65);
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
//设置边框
 $objPHPExcel->getActiveSheet()->getStyle('F1:L2')->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
 $objPHPExcel->getActiveSheet()->getStyle('A3:R3')->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
 $objPHPExcel->getActiveSheet()->getStyle('A7:R7')->getBorders()->getTop()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
 $objPHPExcel->getActiveSheet()->getStyle('C23:E23')->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
 $objPHPExcel->getActiveSheet()->getStyle('M23:Q23')->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
 $objPHPExcel->getActiveSheet()->getStyle('A25:Q25')->getBorders()->getTop()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
 $objPHPExcel->getActiveSheet()->getStyle('A7:R'.$num)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
  $objPHPExcel->getActiveSheet()->getStyle('A7:R'.$num)->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
   $objPHPExcel->getActiveSheet()->getStyle('R7:R'.$num)->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
    $objPHPExcel->getActiveSheet()->getStyle('A'.$num.':R'.$num)->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
 $objPHPExcel->getActiveSheet()->getStyle('A7:R7')->getBorders()->getTop()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);


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