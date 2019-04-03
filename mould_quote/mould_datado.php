<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	if($action == 'add' || $action == 'edit'){

		$mould_name = rtrim($_POST['mould_name']);
		$cavity_type = $_POST['cavity_type'];
		$part_number = turnchar($_POST['part_number']);
		$t_time = rtrim($_POST['t_time']);
		$p_length = $_POST['p_length'];
		$p_width = $_POST['p_width'];
		$p_height = $_POST['p_height'];
		$p_weight = $_POST['p_weight'];
		$drawing_file = turnchar($_POST['drawing_file']);
		$lead_time = rtrim($_POST['lead_time']);
		$m_length = $_POST['m_length'];
		$m_width = $_POST['m_width'];
		$m_height = $_POST['m_height'];
		$m_weight = $_POST['m_weight'];
		$lift_time = $_POST['lift_time'];
		$tonnage = rtrim($_POST['tonnage']);
		$client_name = rtrim($_POST['client_name']);
		$project_name = rtrim($_POST['project_name']);
		$contacts = rtrim($_POST['contacts']);
		$tel = rtrim($_POST['tel']);
		$email = rtrim($_POST['email']);
		//接受数据
		
		$data = $_POST;
		foreach($data as $key=>$value){
			if(is_array($value)){
				$value = implode("$",$value);
			}
			$data[$key] = $value; 
		}
		unset($data['action']);
		unset($data['submit']);
		var_dump($data);
		$m = '';
		$n = '';
		foreach($data as $k=>$v){
			$m .= $k.',';
			$n .= $v.',';
		}
		//$c = substr($m,0,strlen($m)-3);
		echo $m;
		echo $n;
		if($_FILES['file']['name']){
			echo '收到啦';
		} else{
			echo '未收到';
		}
	}
	if($action == 'add'){
		// $sql = "INSERT INTO `db_mould_data` (`mould_dataid`,`mould_name`,`cavity_type`,`part_number`,`t_time`,`p_length`,`p_width`,`p_height`,`p_weight`,`drawing_file`,`lead_time`,`m_length`,`m_width`,`m_height`,`m_weight`,`lift_time`,`tonnage`,`client_name`,`project_name`,`contacts`,`tel`,`email`,`) VALUES (NULL,'$mould_name','$cavity_type','$part_number','$t_time','$p_length','$p_width','$p_height','$p_weight','$drawing_file','$lead_time','$m_length','$m_width','$m_height','$m_weight','$lift_time','$tonnage','$client_name','$project_name','$contacts','$tel','$email')";
		foreach($data as $k=>$v){
			//$sql = "INSERT INTO db_mould_data($k) VALUES($v)";
			//$res = $db->query($sql);
		
		}
		//$sql = "INSERT INTO `db_mould_data`( client_name,project_name,contacts,tel,email,mould_name,k_num,cavity_type,t_time,lead_time,p_length,p_width,p_height,p_weight,m_material,part_number,drawing_file,m_length,m_width,m_height,m_weight,lift_time,tonnage,mould_material, material_specification,materials_number,material_length,material_width,material_height,material_weight, material_unit_price,material_price,total_machining,mould_heat_name,heat_weight,heat_unit_price,heat_price,mold_standard,standard_specification,standard_supplier,standard_number,standard_unit_price, standard_price,  total_standard,mold_design_name,design_hour,design_unit_price,design_price,total_designs,mold_manufacturing,manufacturing_hour,manufacturing_unit_price,manufacturing_price,total_manufacturing,other_fee_name, other_fee_instr,other_fee_price,total_others,management_fee,profit,vat_tax,mold_price_rmb,mold_price_usd,mold_with_vat) VALUES( $client_name,$project_name,$contacts,$tel,$email,$mould_name,$k_num,$cavity_type,$t_time,$lead_time,$p_length,$p_width,$p_height,$p_weight,$m_material,$part_number,$drawing_file,$m_length,$m_width,$m_height,$m_weight,$lift_time,$tonnage,$mould_material,$material_specification,$materials_number,$material_length,$material_width,$material_height,$material_weight,$material_unit_price,$material_price,$total_machining,$mould_heat_name,$heat_weight,$heat_unit_price,$heat_price,$mold_standard,$standard_specification,$standard_supplier,$standard_number,$standard_unit_price,$standard_price,  $total_standard,$mold_design_name,$design_hour,$design_unit_price,$design_price,$total_designs,$mold_manufacturing,$manufacturing_hour,$manufacturing_unit_price,$manufacturing_price,$total_manufacturing,$other_fee_name,$other_fee_instr,$other_fee_price,$total_others,$management_fee,$profit,$vat_tax,$mold_price_rmb,$mold_price_usd,$mold_with_vat)";
		//var_dump($sql);
		
		$sql = "INSERT INTO db_mould_data(client_name,project_name,contacts,tel,email,mould_name,k_num,cavity_type,t_time,lead_time,p_length,p_width,p_height,p_weight,m_material,part_number,drawing_file,m_length,m_width,m_height,m_weight,lift_time,tonnage,mould_material,material_specification,materials_number,material_length,material_width,material_height,material_weight,material_unit_price,material_price,total_machining,mould_heat_name,heat_weight,heat_unit_price,heat_price,mold_standard,standard_specification,standard_supplier,standard_number,standard_unit_price,standard_price,total_standard,mold_design_name,design_hour,design_unit_price,design_price,total_designs,mold_manufacturing,manufacturing_hour,manufacturing_unit_price,manufacturing_price,total_manufacturing,other_fee_name,other_fee_instr,other_fee_price,total_others,management_fee,profit,vat_tax,mold_price_rmb,mold_price_usd,mold_with_vat) VALUES (母士川,18981272483,hr004jtl.com,S,1,1,131,10,200,200,20,705.67,20,101,1321,31,20,13,132,31,313,模架/Mode$型腔/Cavity$型芯/Core$滑块/Slide&Lifter$斜顶/Lifter$镶件/Insert$电极/Electrode,$$$$$$,1$1$1$1$1$1$2,560$310$310$100$100$200$300,560$310$310$100$300$200$300,400$120$120$100$100$300$150,1116$90$90$9$27$107$120,18$70$70$70$70$70$70,20088$6300$6300$630$1890$7490$16800,59498,调质/Tempered$淬火/Hardened$氮化/Nitridation,0$180$0,18$18$18,0$0$0,镶件、日期章/Inserts$顶杆、顶管/Ejection/HotRunner$温控器/TempController$油缸/Hydro-cylinder,11$22$33$44$55$66$77,请选择$请选择$请选择$请选择$请选择$请选择$请选择,1$1$1$1$1$1$1,5000$5000$8000$16000$10000$8000$8000,5000$5000$8000$16000$10000$8000$8000,60000,扫描测绘/Scanning$结构设计/CAD$设计/CAM$分析/CAE,12$12$12$12,100$100$100$100,1200$1200$1200$1200,4800,一般机床/Maching$磨床/Grinding$数控机床/CNC$精密数控机床$线切割/W.C.$电火花/EDM$抛光/Polish$钳工/Fitting$激光烧焊/LaserWelding$皮纹/Texturecost,46$27$55$36$36$46$46$36$36$0,100$100$100$100$100$100$100$100$100$100,4600$2700$5500$3600$3600$4600$4600$3600$3600$0,36400,试模费/Trial,3,2000$1000,54749,8346,16693,26710,191977,33644,218687)";
		$res = $db->query($sql);
		var_dump($res);
		if($db->insert_id){
			header("location:mould_data.php");
		}
	}elseif($action == 'edit'){
		$mould_dataid = $_POST['mould_dataid'];
		$sql = "UPDATE `db_mould_data` SET `mould_name` = '$mould_name',`cavity_type` = '$cavity_type',`part_number` = '$part_number',`t_time` = '$t_time',`p_length` = '$p_length',`p_width` = '$p_width',`p_height` = '$p_height',`p_weight` = '$p_weight',`drawing_file` = '$drawing_file',`lead_time` = '$lead_time',`m_length` = '$m_length',`m_width` = '$m_width',`m_height` = '$m_height',`m_weight` = '$m_weight',`lift_time` = '$lift_time',`tonnage` = '$tonnage',`client_name` = '$client_name',`project_name` = '$project_name',`contacts` = '$contacts',`tel` = '$tel',`email` = '$email' WHERE `mould_dataid` = '$mould_dataid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}
	}elseif($action == 'del'){
		$array_mould_dataid = fun_convert_checkbox($_POST['id']);
		$sql_list = "DELETE `db_mould_quote_list` FROM `db_mould_quote_list` INNER JOIN `db_mould_quote` ON `db_mould_quote`.`quoteid` = `db_mould_quote_list`.`quoteid` WHERE `db_mould_quote`.`mould_dataid` IN ($array_mould_dataid)";
		$db->query($sql_list);
		$sql_quote = "DELETE FROM `db_mould_quote` WHERE `mould_dataid` IN ($array_mould_dataid)";
		$db->query($sql_quote);
		$sql_image = "SELECT `image_filedir`,`image_filename` FROM `db_mould_data` WHERE `mould_dataid` IN ($array_mould_dataid)";
		$result_image = $db->query($sql_image);
		if($result_image->num_rows){
			while($row_image = $result_image->fetch_assoc()){
				$image_filedir = $row_image['image_filedir'];
				$image_filename = $row_image['image_filename'];
				$image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
				$image_big_filepath = "../upload/mould_image/".$image_filedir.'/B'.$image_filename;
				fun_delfile($image_filepath);
				fun_delfile($image_big_filepath);
			}
		}
		$sql = "DELETE FROM `db_mould_data` WHERE `mould_dataid` IN ($array_mould_dataid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>