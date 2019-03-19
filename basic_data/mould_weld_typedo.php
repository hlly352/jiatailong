<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$weld_typename = trim($_POST['weld_typename']);
	if($action == "add"){
		$sql = "INSERT INTO `db_mould_weld_type` (`weld_typeid`,`weld_typename`,`weld_typestatus`) VALUES (NULL,'$weld_typename',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:mould_weld_type.php");
		}
	}elseif($action == "edit"){
		$weld_typeid = $_POST['weld_typeid'];
		$weld_typestatus = $_POST['weld_typestatus'];
		$sql = "UPDATE `db_mould_weld_type` SET `weld_typename` = '$weld_typename',`weld_typestatus` = '$weld_typestatus'  WHERE `weld_typeid` = '$weld_typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$weld_typeid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_mould_weld_type` WHERE `weld_typeid` IN ($weld_typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>