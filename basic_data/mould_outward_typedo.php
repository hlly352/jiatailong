<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$outward_typename = trim($_POST['outward_typename']);
	if($action == "add"){
		$sql = "INSERT INTO `db_mould_outward_type` (`outward_typeid`,`outward_typename`,`outward_typestatus`) VALUES (NULL,'$outward_typename',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:mould_outward_type.php");
		}
	}elseif($action == "edit"){
		$outward_typeid = $_POST['outward_typeid'];
		$outward_typestatus = $_POST['outward_typestatus'];
		$sql = "UPDATE `db_mould_outward_type` SET `outward_typename` = '$outward_typename',`outward_typestatus` = '$outward_typestatus'  WHERE `outward_typeid` = '$outward_typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$outward_typeid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_mould_outward_type` WHERE `outward_typeid` IN ($outward_typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>