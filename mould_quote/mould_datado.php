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
		$part_number = rtrim($_POST['part_number']);
		$t_time = rtrim($_POST['t_time']);
		$p_length = $_POST['p_length'];
		$p_width = $_POST['p_width'];
		$p_height = $_POST['p_height'];
		$p_weight = $_POST['p_weight'];
		$drawing_file = rtrim($_POST['drawing_file']);
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
		$heat_name = rtrim($_POST['heat_name']);
		$heat_weight = rtrim($_POST['heat_weight']);
		$heat_unit_price = rtrim($_POST['heat_unit_price']);
		$heat_price = rtrim($_POST['heat_price']);
		$mould_material = turnchar($_POST['mould_material']);
		$material_specification = turn($_POST['material_specification']);
		$number = turnchar($_POST['number']);
		$unit_price = turnchar($_POST['unit_price']);
		$material_length = turnchar($_POST['material_length']);
		$material_width = turnchar($_POST['material_width']);
		$material_height = turnchar($_POST['material_height']);
		$material_weight = turnchar($_POST['material_weight']);
		$material_price = turnchar($_POST['material_price']);
		$mold_standard = turnchar($_POST['mold_standard']);
		$standard_number = turnchar($_POST['standard_number']);
		$standard_nuit_price = turnchar($_POST['standard_unit_price']);
		$standard_price = turnchar($_POST['standard_price']);
		$standard_specification = turnchar($_POST['standard_specification']);
		$standard_supplier = turnchar($_POST['standard_supplier']);
		$mould_design = turnchar($_POST['mould_design']);
		$design_hour = rtrim($_POST['design_hour']);
		$design_unit_price = rtrim($_POST['design_unit_price']);
		$design_price = rtrim($_POST['design_price']);
		$mold_manufacturing = turnchar($_POST['mold_manufacturing']);
		$manufacturing_hour = turnchar($_POST['manufacturing_hour']);
		$manufacturing_unit_price = turnchar($_POST['manufacturing_unit_price']);
		$manufacturing_price = turnchar($_POST['manufacturing_price']);
		$trial_fee = rtrim($_POST['trial_fee']);
		$freight_fee = rtrim($_POST['freight_fee']);
		$management_fee = rtrim($_POST['mangement_fee']);
		$profit = rtrim($_POST['profit']);
		$vat_tax = rtrim($_POST['vat_tax']);
		$mold_price_rmb = rtrim($_POST['mold_price_rmb']);
		$mold_price_usd = rtrim($_POST['mold_price_usd']);
	}
	if($action == 'add'){
		$sql = "INSERT INTO `db_mould_data` (`mould_dataid`,`mould_name`,`cavity_type`,`part_number`,`t_time`,`p_length`,`p_width`,`p_height`,`p_weight`,`drawing_file`,`lead_time`,`m_length`,`m_width`,`m_height`,`m_weight`,`lift_time`,`tonnage`,`client_name`,`project_name`,`contacts`,`tel`,`email`) VALUES (NULL,'$mould_name','$cavity_type','$part_number','$t_time','$p_length','$p_width','$p_height','$p_weight','$drawing_file','$lead_time','$m_length','$m_width','$m_height','$m_weight','$lift_time','$tonnage','$client_name','$project_name','$contacts','$tel','$email')";
		$db->query($sql);
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