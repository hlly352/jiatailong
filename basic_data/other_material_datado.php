<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$material_typeid = trim($_POST['material_typeid']);
	$material_name = trim($_POST['material_name']);
	$standard_stock = trim($_POST['standard_stock']);

	if($action == "add"){
		$sql = "INSERT INTO `db_other_material_data` (`material_typeid`,`material_name`,`standard_stock`) VALUES ('$material_typeid','$material_name','$standard_stock')";
		$db->query($sql);
		if($db->insert_id){
			header("location:other_material_data.php");
		}
	}elseif($action == "edit"){
		$material_typeid = $_POST['material_typeid'];
		$material_typestatus = $_POST['material_typestatus'];
		$sql = "UPDATE `db_other_material_type` SET `material_typecode` = '$material_typecode',`material_typename` = '$material_typename',`material_typestatus` = '$material_typestatus' WHERE `material_typeid` = '$material_typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$material_typeid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_other_material_data` WHERE `dataid` IN ($material_typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>