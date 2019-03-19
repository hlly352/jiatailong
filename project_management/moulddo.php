<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$clientid = $_POST['clientid'];
	$project_name = str_replace_array(trim($_POST['project_name']));
	$mould_number = trim($_POST['mould_number']);
	$part_name = str_replace_array(trim($_POST['part_name']));
	$plastic_material = str_replace_array(trim($_POST['plastic_material']));
	$shrinkage_rate = $_POST['shrinkage_rate'];
	$cavity_number = trim($_POST['cavity_number']);
	$surface = str_replace_array(trim($_POST['surface']));
	$gate_type = str_replace_array(trim($_POST['gate_type']));
	$core_material = str_replace_array(trim($_POST['core_material']));
	$isexport = $_POST['isexport'];
	$quality_grade = $_POST['quality_grade'];
	$difficulty_degree = $_POST['difficulty_degree'];
	$first_time = $_POST['first_time'];
	$remark = str_replace_array(trim($_POST['remark']));
	$mould_statusid = $_POST['mould_statusid'];
	$projecter = $_POST['projecter'];
	$designer = $_POST['designer'];
	$steeler = $_POST['steeler'];
	$electroder = $_POST['electroder'];
	$assembler = $_POST['assembler'];
	if($action == "add"){
		$sql = "INSERT INTO `db_mould` (`mouldid`,`clientid`,`project_name`,`mould_number`,`part_name`,`plastic_material`,`shrinkage_rate`,`surface`,`cavity_number`,`gate_type`,`core_material`,`isexport`,`quality_grade`,`difficulty_degree`,`first_time`,`remark`,`mould_statusid`,`projecter`,`designer`,`steeler`,`electroder`,`assembler`) VALUES (NULL,'$clientid','$project_name','$mould_number','$part_name','$plastic_material','$shrinkage_rate','$surface','$cavity_number','$gate_type','$core_material','$isexport','$quality_grade','$difficulty_degree','$first_time','$remark','$mould_statusid','$projecter','$designer','$steeler','$electroder','$assembler')";
		$db->query($sql);
		if($db->insert_id){
			header("location:mould.php");
		}
	}elseif($action == "edit"){
		$mouldid = $_POST['mouldid'];
		$sql = "UPDATE `db_mould` SET `clientid` = '$clientid',`project_name` = '$project_name',`mould_number` = '$mould_number',`part_name` = '$part_name',`plastic_material` = '$plastic_material',`shrinkage_rate` = '$shrinkage_rate',`surface` = '$surface',`cavity_number` = '$cavity_number',`gate_type` = '$gate_type',`core_material` = '$core_material',`isexport` = '$isexport',`quality_grade` = '$quality_grade',`difficulty_degree` = '$difficulty_degree',`first_time` = '$first_time',`remark` = '$remark',`mould_statusid` = '$mould_statusid',`projecter` = '$projecter',`designer` = '$designer',`steeler` = '$steeler',`electroder` = '$electroder',`assembler` = '$assembler' WHERE `mouldid` = '$mouldid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$mouldid = fun_convert_checkbox($array_id);
		$sql_image = "SELECT `image_filedir`,`image_filename` FROM `db_mould` WHERE `mouldid` IN ($mouldid)";
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
		$sql = "DELETE FROM `db_mould` WHERE `mouldid` IN ($mouldid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>