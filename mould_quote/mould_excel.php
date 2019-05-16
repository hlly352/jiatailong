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
	//获取图片
	$image_filepath = $row['upload_final_path'];
	  if(stristr($image_filepath,'$') == true){
		  	$image_filepath = substr($image_filepath,0,stripos($image_filepath,"$"));
			}
			$image_path = substr($image_filepath,0,strrpos($image_filepath,'/'));   
			$image_path = str_replace('..','http://localhost',$image_filepath);
			//获取图片到本地
			 $return_content = http_get_data($image_path);  
	//把图片写入文件
	$handle = fopen('pic.jpg','w');
	 fwrite($handle,$return_content);
	fclose($handle);
	/*实例化插入图片类*/
	$objDrawing[0] = new PHPExcel_Worksheet_Drawing();

	/*设置图片路径 切记：只能是本地图片*/
	$objDrawing[0]->setPath('./pic.jpg');
	/*设置图片高度*/
	$objDrawing[0]->setWidth(180);
	$objDrawing[0]->setHeight(70);
	/*设置图片要插入的单元格*/
	$objDrawing[0]->setCoordinates('H7');
	/*设置图片所在单元格的格式*/
	$objDrawing[0]->setOffsetX(30);
	$objDrawing[0]->setOffsetY(10);
	$objDrawing[0]->setRotation(0);
	$objDrawing[0]->getShadow()->setVisible(true);
	$objDrawing[0]->getShadow()->setDirection(50);
	$objDrawing[0]->setWorksheet($objPHPExcel->getActiveSheet());
//型腔数超过1时，获取产品尺寸数据
function getExcelSize($size,$objPHPExcel){
	
	if(strstr($size,'$$')){
		$res = str_replace("$$",PHP_EOL,$size);
		
	} else {
		$res = $size;
		//型腔数为1 时设置行号为10
		$objPHPExcel->getActiveSheet()->getRowDimension(10)->setRowHeight(10);
	}
	return $res;
}
//合并单元格
$objPHPExcel->getActiveSheet()->mergeCells('A1:A5');
$objPHPExcel->getActiveSheet()->mergeCells('B1:E5');
$objPHPExcel->getActiveSheet()->mergeCells('F1:N2');
$objPHPExcel->getActiveSheet()->mergeCells('F3:N5');

$objPHPExcel->getActiveSheet()->mergeCells('A7:E7');
$objPHPExcel->getActiveSheet()->mergeCells('F7:G7');
$objPHPExcel->getActiveSheet()->mergeCells('H7:L12');
$objPHPExcel->getActiveSheet()->mergeCells('M7:N7');
$objPHPExcel->getActiveSheet()->mergeCells('O7:P7');

$objPHPExcel->getActiveSheet()->mergeCells('A8:E8');
$objPHPExcel->getActiveSheet()->mergeCells('F8:G8');
$objPHPExcel->getActiveSheet()->mergeCells('M8:N8');
$objPHPExcel->getActiveSheet()->mergeCells('O8:P8');

$objPHPExcel->getActiveSheet()->mergeCells('A9:E9');	
$objPHPExcel->getActiveSheet()->mergeCells('M9:N9');
$objPHPExcel->getActiveSheet()->mergeCells('O9:P9');

$objPHPExcel->getActiveSheet()->mergeCells('M10:N10');
$objPHPExcel->getActiveSheet()->mergeCells('O10:P10');

$objPHPExcel->getActiveSheet()->mergeCells('A11:E11');
$objPHPExcel->getActiveSheet()->mergeCells('F11:G11');
$objPHPExcel->getActiveSheet()->mergeCells('M11:N11');
$objPHPExcel->getActiveSheet()->mergeCells('O11:P11');

$objPHPExcel->getActiveSheet()->mergeCells('F12:G12');
$objPHPExcel->getActiveSheet()->mergeCells('M12:N12');
$objPHPExcel->getActiveSheet()->mergeCells('O12:P12');
//设置单元格的值
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'JoTyLong ');
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
$objPHPExcel->getActiveSheet()->setCellValue('A7', '模具名称/Mold Specification');
$objPHPExcel->getActiveSheet()->setCellValue('F7', '型腔数量/Cav. Number');
$objPHPExcel->getActiveSheet()->setCellValue('M7', '首次试模时间/T1 Time');
$objPHPExcel->getActiveSheet()->setCellValue('O7', '最终交付时间/Lead Timeme');

$objPHPExcel->getActiveSheet()->setCellValue('A8', $row['mould_name']);
$objPHPExcel->getActiveSheet()->setCellValue('M8', $row['t_time']);
$objPHPExcel->getActiveSheet()->setCellValue('O8', $row['lead_time']);

$objPHPExcel->getActiveSheet()->setCellValue('A9', '产品大小/Part Size (mm)');
$objPHPExcel->getActiveSheet()->setCellValue('F9', '克重/Part Weight(g)');
$objPHPExcel->getActiveSheet()->setCellValue('G9', '材料/Material');
$objPHPExcel->getActiveSheet()->setCellValue('M9', '产品零件号/Part No.');
$objPHPExcel->getActiveSheet()->setCellValue('O9', '数据文件名/Drawing No.');

//把格式设置为文本格式，方便单元格内换行
$objPHPExcel->getActiveSheet()->getStyle('10')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('M10')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->getStyle('O10')->getAlignment()->setWrapText(true);
$objPHPExcel->getActiveSheet()->setCellValue('A10', getExcelSize($row['p_length'],$objPHPExcel));
$objPHPExcel->getActiveSheet()->setCellValue('B10', '*');
$objPHPExcel->getActiveSheet()->setCellValue('C10', getExcelSize($row['p_width'],$objPHPExcel));
$objPHPExcel->getActiveSheet()->setCellValue('D10', '*');
$objPHPExcel->getActiveSheet()->setCellValue('E10', getExcelSize($row['p_height'],$objPHPExcel));
$objPHPExcel->getActiveSheet()->setCellValue('F10', getExcelSize($row['p_weight'],$objPHPExcel));
$objPHPExcel->getActiveSheet()->setCellValue('G10', getExcelSize($row['m_material'],$objPHPExcel));
$objPHPExcel->getActiveSheet()->setCellValue('M10', getExcelSize($row['part_number'],$objPHPExcel));
$objPHPExcel->getActiveSheet()->setCellValue('O10', getExcelSize($row['drawing_file'],$objPHPExcel));

$objPHPExcel->getActiveSheet()->setCellValue('A11', '模具尺寸/Mold Size (mm)');
$objPHPExcel->getActiveSheet()->setCellValue('F11', '模具重量/Mold Weight(Kg)');
$objPHPExcel->getActiveSheet()->setCellValue('M11', '模具寿命/Longevity');
$objPHPExcel->getActiveSheet()->setCellValue('O11', '设备吨位/Press(Ton)');

$objPHPExcel->getActiveSheet()->setCellValue('A12', $row['m_length']);
$objPHPExcel->getActiveSheet()->setCellValue('B12', '*');
$objPHPExcel->getActiveSheet()->setCellValue('C12', $row['m_width']);
$objPHPExcel->getActiveSheet()->setCellValue('D12', '*');
$objPHPExcel->getActiveSheet()->setCellValue('E12', $row['m_height']);
$objPHPExcel->getActiveSheet()->setCellValue('F12', $row['m_weight']);
$objPHPExcel->getActiveSheet()->setCellValue('M12', $row['lift_time']);
$objPHPExcel->getActiveSheet()->setCellValue('O12', $row['tonnage']);

//logo文字垂直居中
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
//设置水平居左
$objPHPExcel->getActiveSheet()->getStyle('P1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('P2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('P4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('A8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('F8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('M8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('O8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$objPHPExcel->getActiveSheet()->getStyle('A10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('C10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('E10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('F10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('G10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('M10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('O10')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

$objPHPExcel->getActiveSheet()->getStyle('A12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('C12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('E12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('F12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('M12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
$objPHPExcel->getActiveSheet()->getStyle('O12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
//设置表格的行高
for($j=1;$j<100;$j++){
	if($j != 10){
		$objPHPExcel->getActiveSheet()->getRowDimension($j)->setRowHeight(10);
		}
	} 
//设置单元格的文本格式
$objPHPExcel->getActiveSheet()->getStyle('P')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
$objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
$objPHPExcel->getActiveSheet()->getStyle('A')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);

//长度不够时，自动换行
$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setWrapText(true);

	
/*
	$objPHPExcel    表格对象
	$data                从数据库中取出的数据
	$start_num	    每个项目开始单元格的行数
	$arr_cols           包含行号，合并单元格信息的数组
	$arr_val             需要写值得字段组成的数组
	$tot_name          小计的类名
	$first_content     每个项目第一个单元格的内容
 */
function mergeCell($objPHPExcel,$data,$start_num,$arr_cols,$arr_val,$tot_name,$first_content){
	$tot_nums = $start_num + 1;
	//设置表头的行高
	$objPHPExcel->getActiveSheet()->getRowDimension($start_num)->setRowHeight(15);
	//设置项目之间间隙的行高
	$objPHPExcel->getActiveSheet()->getRowDimension(6)->setRowHeight(5);
	$objPHPExcel->getActiveSheet()->getRowDimension($start_num-1)->setRowHeight(5);	
	
	//设置第一个单元格垂直居中
	$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	//长度不够时，自动换行
	$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setWrapText(true);
	$num = substr_count($data[$arr_val[0]],'$$') +1;
	//设置第一个的合并多少单元格
	$t_mer = $num+$start_num;
	$tot_mer = $start_num+1;
	$objPHPExcel->getActiveSheet()->mergeCells('A'.$start_num.':B'.$t_mer);
	//设置第一个单元格的内容
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$start_num,$first_content);
	//设置小计合并多少单元格
	$objPHPExcel->getActiveSheet()->mergeCells('P'.$tot_mer.':P'.$t_mer);	
	//获取要填写的数据
	$new_arr = array();
	foreach($arr_val as $key=>$value){
		$new_arr[] = explode('$$',$data[$value]);
		}
	//设置变量，用来统计填写内容遍历了多少次	
	$i = 0;
	foreach($arr_cols as $k=>$v){	
		//合并表头的单元格
		if(is_array($v)){	
			$objPHPExcel->getActiveSheet()->mergeCells($k.$start_num.':'.$v[1].$start_num);
			//设置表头的值
			$objPHPExcel->getActiveSheet()->setCellValue($k.$start_num,$v[0]);
			//设置表头垂直居中
			$objPHPExcel->getActiveSheet()->getStyle($k.$start_num)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			} else {
		//设置表头的值
		$objPHPExcel->getActiveSheet()->setCellValue($k.$start_num,$v);
		//设置表头自动换行
		$objPHPExcel->getActiveSheet()->getStyle($k.$start_num)->getAlignment()->setWrapText(true);
		//设置表头垂直居中
		$objPHPExcel->getActiveSheet()->getStyle($k.$start_num)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		 }
		
		//填写数据
		foreach($new_arr[$i] as $keys=>$vals){
			$no = $keys+$start_num+1;
			
			//填写数据的单元格合并
			if(!empty($v[2]) && is_array($v)){
				$objPHPExcel->getActiveSheet()->mergeCells($k.$no.':'.$v[2].$no);
			}
			//设置填充数据的单元格左对齐
			$objPHPExcel->getActiveSheet()->getStyle($k.$no)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);	
				if($arr_val[0] == 'mould_material'){
					//最后三列的金额加上人民币符号
					 if($i>count($new_arr)-3){
						$objPHPExcel->getActiveSheet()->setCellValue($k.$no, '￥'.$vals);
				   	}else{
						$objPHPExcel->getActiveSheet()->setCellValue('I'.$no,'*');
						$objPHPExcel->getActiveSheet()->setCellValue('K'.$no,'*');
						$objPHPExcel->getActiveSheet()->setCellValue($k.$no, $vals);
					}
				} else {
					if($i>count($new_arr)-3){
					     	$objPHPExcel->getActiveSheet()->setCellValue($k.$no, '￥'.$vals);
					} else{
					    	$objPHPExcel->getActiveSheet()->setCellValue($k.$no, $vals);
					}
				}	
			
		}
		//判断数据遍历的次数
		if($i<count($new_arr)-1){
			$i++;
			}
		}
		//垂直居中
		$objPHPExcel->getActiveSheet()->getStyle('P'.$tot_mer)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
		//设置边框
		$objPHPExcel->getActiveSheet()->getStyle('A'.$start_num.':P'.$no)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
		//设置最外面的粗边框
		 $objPHPExcel->getActiveSheet()->getStyle('A'.$start_num.':P'.$start_num)->getBorders()->getTop()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
		  $objPHPExcel->getActiveSheet()->getStyle('A'.$start_num.':P'.$start_num)->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
		  $objPHPExcel->getActiveSheet()->getStyle('A'.$no.':P'.$no)->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
		  $start_nums = $start_num + 1;
		  $objPHPExcel->getActiveSheet()->getStyle('P'.$start_num.':P'.$no)->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
		  $objPHPExcel->getActiveSheet()->getStyle('P'.$start_num.':P'.$no)->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
		  $objPHPExcel->getActiveSheet()->getStyle('C'.$start_num.':C'.$no)->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THICK);
		
		//设置小计的值
		$objPHPExcel->getActiveSheet()->setCellValue('P'.$tot_nums, '￥'.$data[$tot_name]);		
		//返回下一个项目的开始行数
		return $no+2;
		}
//加工材料

$material_arr = [
	'C'=>['材料名称/Material','E','E'],
	'F'=>'材料牌号/Specification',
	'G'=>'数量/Number',
	'H'=>['尺寸/Size(mm*mm*mm)','L'],
	'J'=>' ',  
	'L'=>' ',
	'M'=>'总重量/Weight(kg)',
	'N'=>'单价(元)/Unit Price',
	'O'=>'金额/Price(RMB)',
	'P'=>'小计(元)'
];
$material_key = ['mould_material','material_specification','materials_number','material_length','material_width','material_height','material_weight','material_unit_price','material_price'];
$heat_num = mergeCell($objPHPExcel,$row,14,$material_arr,$material_key,'total_machining','材料加工费/Machining Materia');
//热处理
$heat_arr = [
	'C'=>['热处理名称/Item','E','E'],
	'F'=>['重量/weight(kg)','G','G'],
	'H'=>['	单价/Unit Price(RMB)','M','M'],
	'N'=>['金额/Price(RMB)','O','O'],
	'P'=>'小计(元)'
];
$heat_key = ['mould_heat_name','heat_weight','heat_unit_price','heat_price'];
$standard_num = mergeCell($objPHPExcel,$row,$heat_num,$heat_arr,$heat_key,'total_heat','热处理/Heat Treatment');
//模具配件
$standard_arr = [
	'C'=>['装配件/Item','E','E'],
	'F'=>['规格型号/Specification','G','G'],
	'H'=>['品牌/Supplier','L','L'],
	'M'=>'数量/Number',
	'N'=>'单价(元)/Unit Price',
	'O'=>'金额(RMB)/price',
	'P'=>'小计(元)'
];
$standard_key = ['mold_standard','standard_specification','standard_supplier','standard_number','standard_unit_price','standard_price'];
$design_num = mergeCell($objPHPExcel,$row,$standard_num,$standard_arr,$standard_key,'total_standard','模具配件/Mold standard parts');
//获取型腔数量

if(strpos($row['cavity_type'],'$$')){
		$cavity_nums = str_replace('$$','+',$row['cavity_type']);
	} else {
		$cavity_nums = '1*'.$row['cavity_type'];
	}
$objPHPExcel->getActiveSheet()->setCellValue('F7', $row['k_num'].'k |'.$cavity_nums);


//设置表格的列宽
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(6.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(1);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(6.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(1);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(6.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(6.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(6.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(6.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(1);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(6.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(1);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(6.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(6.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(6.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(12.75);
$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(12.75);

//设置表格的字体
$objPHPExcel->getActiveSheet()->getStyle('A1:P100')->getFont()->setName('宋体')->setSize(5);
//logo文字字体加粗
$objPHPExcel->getActiveSheet()->getStyle('A2')->getFont()->setName('Arial')->setBold(true);


//添加图片
/*实例化插入图片类*/
$objDrawing = new PHPExcel_Worksheet_Drawing();
/*设置图片路径 切记：只能是本地图片*/
$objDrawing->setPath('../jtl.png');
/*设置图片高度*/
$objDrawing->setWidth(100);
$objDrawing->setHeight(50);
/*设置图片要插入的单元格*/
$objDrawing->setCoordinates('B1');
/*设置图片所在单元格的格式*/
$objDrawing->setOffsetX(10);
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