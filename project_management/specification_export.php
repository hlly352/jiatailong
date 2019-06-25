<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';

echo '<meta charset="utf-8">';
$action = $_GET['action'];
if($action == 'show'){
	$specification_id = $_GET['specification_id'];
	$shrink = $_GET['shrink'];

	//通过id查询模具信息
	$sql = "SELECT * FROM `db_mould_specification` WHERE `mould_specification_id` = '$specification_id'";
	$res = $db->query($sql);
	if($res->num_rows){
		$row = $res->fetch_assoc();
	}

//通过模具id查询模具的图片
$sql_image = "SELECT `upload_final_path` FROM `db_mould_data` WHERE `mould_dataid` = {$row['mould_id']}";
$image_info = $db->query($sql_image);
if($image_info->num_rows){
	$row['image_name'] = $image_info->fetch_row()[0];
}
 //查找负责人员
$depart_name = array('boss'=>'总经办','saler'=>'市场部','projecter'=>'项目部','designer'=>'设计部','programming'=>'CNC','assembler'=>'钳工');
foreach($depart_name as $k=>$v){
    $sql_employee = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name` FROM `db_employee` LEFT JOIN `db_department` as saler ON `db_employee`.deptid = saler.`deptid` WHERE `db_employee`.`employee_status`='1' AND saler.`dept_name` LIKE '%$v%'";
    $res = $db->query($sql_employee);
   
    ${$k} = array();
    
    if($res->num_rows){
      while($rows = $res->fetch_row()){
        ${$k}[] = $rows;
      }
      
    }
    if($k !='boss'){
      ${$k} = array_merge(${$k},$boss);
    }
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
	$image_filepath = $row['image_name'];
	  if(stristr($image_filepath,'$') == true){
		  	$image_filepath = substr($image_filepath,0,stripos($image_filepath,"$"));
			} 
			$image_path = substr($image_filepath,0,strrpos($image_filepath,'/'));   
			$image_path = str_replace('..','http://192.168.1.3:808',$image_filepath);
			//获取图片到本地
			 $return_content = http_get_data($image_path);  
	//把图片写入文件
	$handle = fopen('pic.jpg','w');
	 fwrite($handle,$return_content);
	fclose($handle);
	/*实例化插入图片类*/
	$objDrawing = new PHPExcel_Worksheet_Drawing();

	/*设置图片路径 切记：只能是本地图片*/
	$objDrawing->setPath('./pic.jpg');
	//图片不按照比例缩放
	$objDrawing->setResizeProportional(false);
	/*设置图片高度*/
	$objDrawing->setWidth(150);
	$objDrawing->setHeight(62);
	/*设置图片要插入的单元格*/
	$objDrawing->setCoordinates('E4');
	/*设置图片所在单元格的格式*/
	$objDrawing->setOffsetX(10);
	$objDrawing->setOffsetY(5);
	$objDrawing->setRotation(0);
	$objDrawing->getShadow()->setVisible(true);
	$objDrawing->getShadow()->setDirection(50);
	$objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
	//合并单元格
	$objPHPExcel->getActiveSheet()->mergeCells('A1:F2');
	$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');

	//设置单元格的值	
	$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->setCellValue('A1', "苏州嘉泰隆实业有限公司\n模具规格书");
	$objPHPExcel->getActiveSheet()->setCellValue('A3', '基本信息');
	$objPHPExcel->getActiveSheet()->setCellValue('E3', '客户合同编号');
	$objPHPExcel->getActiveSheet()->setCellValue('F3', $row['customer_order_no']);
	//水平居中
	$objPHPExcel->getActiveSheet()->getStyle('E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle('F3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

if($shrink == 'undefined'){
	$objPHPExcel->getActiveSheet()->mergeCells('E4:F8');
	$objPHPExcel->getActiveSheet()->mergeCells('A9:F9');
	$suggestions = explode('$$',$row['suggestion']);
	$objPHPExcel->getActiveSheet()->setCellValue('E10', '意见');
	$objPHPExcel->getActiveSheet()->setCellValue('E11', '意见');
	$objPHPExcel->getActiveSheet()->setCellValue('E12', '意见');
	$objPHPExcel->getActiveSheet()->setCellValue('F10', $suggestions[0]);
	$objPHPExcel->getActiveSheet()->setCellValue('F11', $suggestions[1]);
	$objPHPExcel->getActiveSheet()->setCellValue('F12', $suggestions[2]);
	$set_a = array(4=>'客户代码','模具编号','任务内容','启动时间','验收时间','负责人与审核','销售','项目','实施');
	//获取销售
	$saler_employee = explode('$$',$row['saler']);
	foreach($saler as $k=>$v){
		if($v[0] == $saler_employee[0]){
			$salers = $v[1];
			break;
		}else{
			$salers = '';
		}
	}
	//获取项目
	$projecter_employee = explode('$$',$row['projecter']);
	foreach($projecter as $k=>$v){
		if($v[0] == $projecter_employee[0]){
			$projecters = $v[1];
			break;
		}else{
			$projecters = '';
		}
	}
	//获取部门经理
	$managers = explode('$$',$row['manager']);
	$manager_employee = array();
	foreach($managers as $k=>$v){
		$manager_sql = "SELECT `employee_name` FROM `db_employee` WHERE `employeeid`=".$v;
		$manager_res = $db->query($manager_sql);
		if($manager_res->num_rows){
			$manager_employee[$k] = $manager_res->fetch_row()[0];
		}
	}
	$set_b = array(4=>$row['customer_code'],$row['mould_no'],$row['cavity_num'],$row['start_time'],$row['check_time'],' ',$salers,$projecters,$row['do_task']);
	$set_c = array(4=>'项目名称','产品名称','图纸编号','塑胶材料','完成时间',' ','部门经理','部门经理','部门经理');
	$set_d = array(4=>$row['project_name'],$row['mould_name'],$row['drawing_type'],$row['material_other'],$row['finish_time'],' ',$manager_employee[0],$manager_employee[1],$manager_employee[2]);
	//设置每一列的值
	function setColVal($col_arr,$col_num,$objPHPExcel){
		foreach($col_arr as $k=>$v){
			$objPHPExcel->getActiveSheet()->setCellValue($col_num.$k, $v);
		}
	}
	//调用函数
	setColVal($set_a,'A',$objPHPExcel);
	setColVal($set_b,'B',$objPHPExcel);
	setColVal($set_c,'C',$objPHPExcel);
	setColVal($set_d,'D',$objPHPExcel);
    //设置边框
     $objPHPExcel->getActiveSheet()->getStyle('A3:F12')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
     $arr_cols_num = array('A','B','C','D','E','F');
     foreach($arr_cols_num as $k=>$v){
     	for($i=1;$i<13;$i++){
     		if(!($i==3||$i==9)){
			//水平居中
				$objPHPExcel->getActiveSheet()->getStyle($v.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			}
		     //设置高度
				$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(10);
			//设置宽度
			$objPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth(14);
			//垂直居中
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
		}
	}
			$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
		//设置单元格字体和字号
		$objPHPExcel->getActiveSheet()->getStyle('A3:F12')->getFont()->setName('Arial')->setSize(6);
		$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('Arial')->setSize(10)->setBold(true);
		//设置背景色
		$objPHPExcel->getActiveSheet()->getStyle('A4:A8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
		$objPHPExcel->getActiveSheet()->getStyle('C4:C8')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
		$objPHPExcel->getActiveSheet()->getStyle('A10:A12')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
		$objPHPExcel->getActiveSheet()->getStyle('C10:C12')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
		$objPHPExcel->getActiveSheet()->getStyle('E10:E12')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
		//设置字体
		$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setName('Arial')->setSize(6)->setBold(true);
		$objPHPExcel->getActiveSheet()->getStyle('A9')->getFont()->setName('Arial')->setSize(6)->setBold(true);
	}else{
	$objPHPExcel->getActiveSheet()->mergeCells('E4:F9');
	$objPHPExcel->getActiveSheet()->mergeCells('A10:F10');
//T几模
$t_num = explode('$$',$row['t_num']);

//A列的文字信息
$arr_a = array('客户代码','模具编号','型腔数','启动时间','首板时间','预计走模时间','注塑机及周边匹配信息','机器品牌','定位环直径','集水块接头规格','集油块接头规格','模具布局','模具要求','型腔/型芯方式','难度系数','模具是否出口','进胶、冷却加热、抽芯、顶出等','				
浇口类型','热流道品牌','顶出系统','模具材料及要求','项目','模架','模架A板','模架B板','模架顶针板','型腔','型芯','滑块','斜顶','镶件','配件标准','项目','标准件','日期章','油缸','皮纹','试模打样','客户参与试模','试模、打样胶料','产品检查报告','T1:'.$t_num[0].'模','走模要求','是否移模','模具外观喷漆','吊环、备件、电极、末次样品','零件检查报告、走模前检查报告','走模装箱照片、视频','客户处交模、验模','流程控制','产品设计','2D模具结构设计图','项目启动会','零件加工工艺评审会','项目进度汇报','草图及重点提示',' ','负责人与审核','销售','项目','设计','编程','装配');


//每个栏目的标题
$arr_a_num = array(3,10,15,20,24,35,41,46,53,59,61);
//设置第A列的文字内容
$objPHPExcel->getActiveSheet()->mergeCells('A3:D3');
$objPHPExcel->getActiveSheet()->getStyle('A3')->getFont()->setName('Arial')->setSize(10)->setBold(true);
foreach($arr_a as $k=>$v){
	$key = $k + 4;
	//合并每个栏目的标题
	if(in_array($key,$arr_a_num)){
		$objPHPExcel->getActiveSheet()->mergeCells('A'.$key.':F'.$key);
		$objPHPExcel->getActiveSheet()->getStyle('A'.$key)->getFont()->setName('Arial')->setSize(10)->setBold(true);
	}
	$objPHPExcel->getActiveSheet()->setCellValue('A'.$key, $v);
}

function getCheckbox($str,$row,$array_config){
	$val = array();
	if(strpos($row[$str],'$')){
		$val = explode('$',$row[$str]);
	}else{
		$val[0] = $row[$str];
	}
	$info = ' ';
	foreach($val as $v){
	  if(!empty($v) || $v == '0'){
	  	$v = intval($v);
	  	$info .= $array_config[$v].'/';
		}
	}
	$info = substr($info,0,strlen($info)-1);
	return $info;
}
//多选框
$checkbox_arr = array('install_way','product_check','mould_type','mould_group','ejection_system','sepcial_cool','cool_medium','action_plate','require','customer_plate','mould_ring','drawing_check');
foreach($checkbox_arr as $k=>$v){
$row[$v] = getCheckbox($v,$row,${'array_'.$v});
}
//单选框
$array_radio = array('is_export','customer_join','customer_require','customer_water','is_move','steel_material','mould_photo','sample_check','mould_check','photo_vedio','customer_try','product_design','mould_analyse','dfm_report','drawing_2d','drawing_3d','project_start','product_judge','mould_judge','machining_judge');
foreach($array_radio as $k=>$v){
	if($row[$v] == '0'){
		$row[$v] = '否';
	} elseif($row[$v] == '1'){
		$row[$v] = '是';
	}
}

//下拉选项框
$array_select = array('mould_way','drawing_standard','injection_type','mould_require','quality_degree','difficulty_degree','needle_type','runner_type','hot_runner_supplier','pickup_way','draw_material','draw_post','pack_method','hand_over','settle_way','cavity_mode','surface_spray','mould_handbook','mould_pack','mould_transport','service_fee','judge_method','customer_confirm','error_report','project_progress');
foreach($array_select as $k=>$v){
	if(strlen($row[$v]) == 0){
		$row[$v] = ' ';
	} else {
		$index = intval($row[$v]);
		$row[$v] = ${'array_'.$v}[$index];
	}
}
//获取数组下拉框的值(数组形式传值)
function getArr($arrs,$string,$row){
	$new_row = explode('$$',$row[$string]);
	${$string} = array();
	foreach($new_row as $k=>$v){
			
		if(strlen($v) == 0 ){
		   
			${$string}[$k] = ' ';
		}else{
		   ${$string}[$k] = $arrs[$v];
		   
		}
	}
		 return ${$string};
}
$arr_key = array('material_supplier','material_specification','material_hard','special_handle','surface_require');
foreach($arr_key as $k=>$v){
	${$v} = getArr(${'array_'.$v},$v,$row);
}

//设置第B列的值
function getVal($cols,$arr,$row,$objPHPExcel){
	$arr_x = array();
	foreach($arr as $k=>$v){
		$arr_x[$k] = $row[$v];
	}
	foreach($arr_x as $key=>$val){
		$key = $key + 4;
		$objPHPExcel->getActiveSheet()->setCellValue($cols.$key, $val);
	}
}
//material_supplier 是进口还是国产
$arr_row = explode('$$',$row['material_supplier']);
$country = array();
foreach($arr_row as $k=>$v){
	if($k>3){
		if(!empty($v) || $v=='0'){
		 $country[] = $array_material_county[$v];
		}else{
		 $country[] = ' ';
		}
	}
}
//获取销售，项目，设计，编程，装配的最新负责人
$duty_arr = array('saler','projecter','designer','programming','assembler');
$managers = explode('$$',$row['manager']);
$manager = array();
foreach($duty_arr as $k=>$v){
	//获取部门经理的姓名
	if(strlen($managers[$k])==0){
		$manager[$k] = ' ';
	}else{
		foreach(${$v} as $key=>$value){
			if($value[0] == $managers[$k]){
				$manager[$k] = ${$v}[$key][1];
			}
		}
	}
	//获取部门员工的姓名
	if(strpos('$$',$row[$v])){
		$new_duty = explode('$$',$row[$v]);
		$i = $new_duty[0];
		if(!empty($i)){
			foreach(${$v} as $ks=>$vs){
				if($vs[0]==$i){
					${$v} = ${$v}[$ks][1];
						}
					}
		} else {
			${$v} = ' ';
		}
	}else{
		foreach(${$v} as $kes=>$vas){
				if($vas[0]==$row[$v]){
			${$v} = ${$v}[$kes][1];
		}
	}
	}
		if(is_array(${$v})){
			${$v} = ' ';
		}

}

//设置第b 列的文字内容
//supplier变为数组

	$supplier = explode('$$',$row['supplier']);

$arr_b = array($row['customer_code'],$row['mould_no'],$row['cavity_num'],$row['start_time'],$row['check_time'],$row['finish_time'],7=>$row['machine_supplier'],$row['locator'],$row['catchment'],$row['oil_collection'],12=>$row['mould_require'],$row['cavity_mode'],$row['difficulty_degree'],$row['is_export'],17=>$row['injection_type'],$row['hot_runner_supplier'],$row['ejection_system'],' ','材料品牌',$material_supplier[0],$material_supplier[1],$material_supplier[2],$material_supplier[3],$country[0],$country[1],$country[2],$country[3],$country[4],' ','品牌',$array_water_connector[$supplier[0]],$array_supplier[$supplier[2]],$array_cylinder[$supplier[4]],$array_skin_texture[$row['skin_texture']],' ',$row['customer_join'],$row['draw_material'],$row['product_check'],'T2:'.$t_num[1].'模',43=>$row['is_move'],$row['surface_spray'],$row['mould_ring'],$row['mould_check'],$row['photo_vedio'],$row['customer_try'],50=>$row['product_design'],$row['drawing_2d'],$row['project_start'],$row['machining_judge'],$row['project_progress'],58=>$saler,$projecter,$designer,$programming,$assembler);

foreach($arr_b as $k=>$v){
	$key = $k + 4;
	$objPHPExcel->getActiveSheet()->setCellValue('B'.$key, $v);
}
//设置d列文字内容
//把部门经理的id取出来
// $managers = array();
// $managers = explode('$$',$row['manager']);
// foreach($managers as $k=>$v){
// 	foreach($manager as $ks=>$vs){
// 		if($vs[1] == $v){
// 			$manager[$k] = $vs[1]; 
// 		}else{
// 			$manager[$k] = ' ';
// 		}
// 	}

// }

//唧嘴
$ji = $row['ji_sr'];
if($ji == '0'){
	$sr = '90度';
}elseif($ji == '1'){
	$sr = '平面';
}else{
	$sr = $ji;
}

$arr_d = array(4=>$row['project_name'],$row['mould_name'],$row['drawing_type'],$row['material_other'],$row['shrink'],$row['require'],11=>$row['machine_tonnage'].'T',$sr,$row['electron_valve'],$row['temperature_control'],16=>$row['mould_type'],$row['mould_group'],$row['quality_degree'],'模号:'.$row['is_reference'],21=>$row['needle_type'],$row['cool_medium'],$row['pickup_way'],25=>'材料硬度',$material_hard[0],$material_hard[1],$material_hard[2],$material_hard[3],$material_hard[4],$material_hard[5],$material_hard[6],$material_hard[7],$material_hard[8],36=>'项目','水管接头','电子阀接头','气动接头','油压接头',42=>$row['customer_require'],$row['draw_num'].'次 共'.$row['total_num'].'次',$row['pack_method'],'T4:'.$t_num[3].'模',47=>$row['hand_over'],$row['action_plate'],$row['mould_handbook'],$row['sample_check'],$row['mould_pack'],$row['service_fee'],54=>$row['mould_analyse'],$row['drawing_3d'],$row['product_judge'],$row['judge_method'],$row['error_report'],62=>$manager[0],$manager[1],$manager[2],$manager[3],$manager[4]);
foreach($arr_d as $k=>$v){
	$objPHPExcel->getActiveSheet()->setCellValue('D'.$k, $v);
}
$specification = array();
$row['specification'] = explode('$$',$row['specification']);
foreach($row['specification'] as $k=>$v){
	$specification[$k] = $v;
}

$suggestion = array();
$row['suggestion'] = explode('$$',$row['suggestion']);
foreach($row['suggestion'] as $k=>$v){
	$suggestion[$k] = $v;
}
//C列的文字信息
$arr_c = array('项目名称','产品名称','图纸编号','塑胶材料','产品缩水率','重点要求',' 
	','机器吨位','唧嘴SR','电子阀接头规格','热流道温控箱接头规格',' ','模具类型','组合互换','质量等级','备模或类似参考',' ','阀针类型','冷却加热介质','取件方式',' ','材料牌号',$material_specification[0],$material_specification[1],$material_specification[2],$material_specification[3],$material_specification[4],$material_specification[5],$material_specification[6],$material_specification[7],$material_specification[8],32=>'规格',$specification[0],$specification[2],$specification[4],$specification[6],38=>'严格按客户要求试模','免费样品数量/次数','包装方式','T3:'.$t_num[2].'模',43=>'模具交付目的地','热流道、运水、动作铭牌','模具手册、2D图纸、数据光盘','试模报告、样品检测报告','模具包装方式','售后服务',' ','模流分析','全3D模具图','产品评审会','客户评审方式','出错汇报',58=>'部门经理','部门经理','部门经理','部门经理','部门经理');
//设置第c 列的文字内容
foreach($arr_c as $k=>$v){
	$key = $k + 4;
	$objPHPExcel->getActiveSheet()->setCellValue('C'.$key, $v);
}
//E列的文字信息
$arr_e = array(7=>'模具装夹方式','KO直径、螺牙','气阀接头规格','其他要求',' ','模具形式','图纸标准','模具寿命','成型周期',' ','流道类型','特殊冷却加热','其它要求',' ','特殊处理',$special_handle[0],$special_handle[1],$special_handle[2],$special_handle[3],$special_handle[4],$special_handle[5],$special_handle[6],$special_handle[7],$special_handle[8],32=>'品牌',$array_water_connector[$supplier[1]],$array_air_connector[$supplier[3]],$array_air_connector[$supplier[5]],$array_oil_connector[$supplier[6]],38=>'客户是否需要走水板','寄样方式','其它事项','T5:'.$t_num[4].'模',43=>'交货结算方式','客户及我司铭牌','钢材材质证明、热处理证明','末次试模照片、视频','模具运输方式','其他要求',' ','DFM报告','图纸检查对照表','模具结构评审会','客户确认图纸方式','其它要求',58=>'意见','意见','意见','意见','意见');
//设置第e 列的文字内容
foreach($arr_e as $k=>$v){
	$key = $k + 4;
	$objPHPExcel->getActiveSheet()->setCellValue('E'.$key, $v);
}
//设置f列的文字信息
$arr_f = array(11=>$row['install_way'],'直径:'.$row['screw_diameter'].'mm 螺牙:'.$row['screw'].'M',$row['air_valve'],$row['other_require'],16=>$row['mould_way'],$row['drawing_standard'],$row['mould_life'].'万',$row['moding_cycle'].'S',21=>$row['runner_type'],$row['sepcial_cool'],$row['ejection_require'],25=>'表面要求',$surface_require[0],$surface_require[1],$surface_require[2],$surface_require[3],$surface_require[4],$surface_require[5],$surface_require[6],$surface_require[7],$surface_require[8],36=>'规格',$specification[1],$specification[3],$specification[5],$specification[7],42=>$row['customer_water'],$row['draw_post'],$row['other_thing'],'T6'.$t_num[5].'模',47=>$row['settle_way'],$row['customer_plate'],$row['steel_material'],$row['mould_photo'],$row['mould_transport'],$row['go_mould_require'],54=>$row['dfm_report'],$row['drawing_check'],$row['mould_judge'],$row['customer_confirm'],$row['control_require'],62=>$suggestion[0],$suggestion[1],$suggestion[2],$suggestion[3],$suggestion[4]);
foreach($arr_f as $k=>$v){
	$objPHPExcel->getActiveSheet()->setCellValue('F'.$k, $v);
}
//水平居中
$arr_horizontal = array('A','B','C','D','E','F');
foreach($arr_horizontal as $v){
	for($i=1;$i<68;$i++){
		//设置高度
		$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(10);
		if(!in_array($i,$arr_a_num)){
			//水平居中
			$objPHPExcel->getActiveSheet()->getStyle($v.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		}
	}
	//设置宽度
	$objPHPExcel->getActiveSheet()->getColumnDimension($v)->setWidth(14);
}
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
//垂直居中
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

//设置单元格字体和字号
$objPHPExcel->getActiveSheet()->getStyle('A3:F66')->getFont()->setName('Arial')->setSize(6);
$objPHPExcel->getActiveSheet()->getStyle('A1:F2')->getFont()->setName('Arial')->setSize(10)->setBold(true);
//设置边框
 $objPHPExcel->getActiveSheet()->getStyle('A3:F59')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
 $objPHPExcel->getActiveSheet()->getStyle('A60')->getBorders()->getLeft()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
 $objPHPExcel->getActiveSheet()->getStyle('F60')->getBorders()->getRight()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
 $objPHPExcel->getActiveSheet()->getStyle('A61:F66')->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
//设置单元格背景色
$arr_back = array('A4:A9','C4:C9','A11:A14','C11:C14','E11:E14','A16:A19','C16:C19','E16:E19','A21:A23','C21:C23','E21:E23','A25:F25','A26:A34','A36:F36','A37:A40','D37:D40','A42:A44','C42:C44','E42:E44','A47:A52','C47:C52','E47:E52','A54:A58','C54:C58','E54:E58','A62:A66','C62:C66','E62:E66');
foreach($arr_back as $v){
	$objPHPExcel->getActiveSheet()->getStyle($v)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('DEDEDE');
}
//插入多张图片
if(!empty($row['upload_final_path'])){
	$objPHPExcel->getActiveSheet()->getRowDimension(60)->setRowHeight(40);
	$img_file = explode('$',$row['upload_final_path']);
	array_pop($img_file);
	$host_name = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER["SERVER_PORT"];
	foreach($img_file as $k=>$v){
		$image_path = str_replace('..',$host_name,$v);
		//获取图片到本地
		$return_content = http_get_data($image_path);
		//把图片写入文件
		$handle = fopen('img'.$k.'.jpg','w');
		fwrite($handle,$return_content);
		fclose($handle);
		//添加图片
		/*实例化插入图片类*/
		$objDraw[$k] = new PHPExcel_Worksheet_Drawing();
		
		/*设置图片路径 切记：只能是本地图片*/
		$objDraw[$k]->setPath('./img'.$k.'.jpg');
		/*设置图片高度*/
		$objDraw[$k]->setResizeProportional(false);
		$objDraw[$k]->setWidth(75);
		$objDraw[$k]->setHeight(45);
		/*设置图片要插入的单元格*/
		$objDraw[$k]->setCoordinates('F60');
		/*设置图片所在单元格的格式*/
		$offset = (-80)*(6-$k);
		$objDraw[$k]->setOffsetX($offset);
		$objDraw[$k]->setOffsetY(5);
		$objDraw[$k]->setRotation(0);
		$objDraw[$k]->getShadow()->setVisible(true);
		$objDraw[$k]->getShadow()->setDirection(50);
		$objDraw[$k]->setWorksheet($objPHPExcel->getActiveSheet());
		}
	}
}
//添加图片
/*实例化插入图片类*/
$objDrawings = new PHPExcel_Worksheet_Drawing();
/*设置图片路径 切记：只能是本地图片*/
 $objDrawings->setPath('../jtl.png');
/*设置图片高度*/
 $objDrawings->setHeight(30);
 //$objDrawings->setWidth(20);
/*设置图片要插入的单元格*/
 $objDrawings->setCoordinates('A1');
/*设置图片所在单元格的格式*/
 $objDrawings->setOffsetX(10);
 $objDrawings->setOffsetY(5);
 $objDrawings->setRotation(0);
 $objDrawings->getShadow()->setVisible(true);
 $objDrawings->getShadow()->setDirection(50);
 $objDrawings->setWorksheet($objPHPExcel->getActiveSheet());
//页边距水平居中
$objPHPExcel->getActiveSheet()->getPageSetup()->setHorizontalCentered(true);
//设置时间格式
$now_time = date('Ymd',time());
$titles = $row['project_name'].'-'.$now_time.".xls";

// 输出Excel表格到浏览器下载
 ob_end_clean();
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename='.$titles);
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