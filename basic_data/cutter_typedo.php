<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$type = trim($_POST['type']);
	if($action == "add"){
		$sql = "INSERT INTO `db_cutter_type` (`typeid`,`type`) VALUES (NULL,'$type')";
		$db->query($sql);
		if($db->insert_id){
			header("location:cutter_type.php");
		}
	}elseif($action == "edit"){
		$typeid = $_POST['typeid'];
		$type_status = $_POST['type_status'];
		$sql = "UPDATE `db_cutter_type` SET `type` = '$type' WHERE `typeid` = '$typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}
	}elseif($action == "del"){
		$array_typeid = fun_convert_checkbox($_POST['id']);
		$sql = "DELETE FROM `db_cutter_type` WHERE `typeid` IN ($array_typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>