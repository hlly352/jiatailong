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
		var_dump($_POST);
		if($_FILES['file']['name']){
			echo '收到啦';
		} else{
			echo '未收到';
		}
	}
	if($action == 'add'){
		$sql = "INSERT INTO `db_mould_data` (`mould_dataid`,`mould_name`,`cavity_type`,`part_number`,`t_time`,`p_length`,`p_width`,`p_height`,`p_weight`,`drawing_file`,`lead_time`,`m_length`,`m_width`,`m_height`,`m_weight`,`lift_time`,`tonnage`,`client_name`,`project_name`,`contacts`,`tel`,`email`,`) VALUES (NULL,'$mould_name','$cavity_type','$part_number','$t_time','$p_length','$p_width','$p_height','$p_weight','$drawing_file','$lead_time','$m_length','$m_width','$m_height','$m_weight','$lift_time','$tonnage','$client_name','$project_name','$contacts','$tel','$email')";
		var_dump($sql);
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