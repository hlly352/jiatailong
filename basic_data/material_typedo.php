<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$material_typecode = trim($_POST['material_typecode']);
	$material_typename = trim($_POST['material_typename']);
	if($action == "add"){
		$sql = "INSERT INTO `db_material_type` (`material_typeid`,`material_typecode`,`material_typename`,`material_typestatus`) VALUES (NULL,'$material_typecode','$material_typename',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:material_type.php");
		}
	}elseif($action == "edit"){
		$material_typeid = $_POST['material_typeid'];
		$material_typestatus = $_POST['material_typestatus'];
		$sql = "UPDATE `db_material_type` SET `material_typecode` = '$material_typecode',`material_typename` = '$material_typename',`material_typestatus` = '$material_typestatus' WHERE `material_typeid` = '$material_typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$material_typeid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_material_type` WHERE `material_typeid` IN ($material_typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>