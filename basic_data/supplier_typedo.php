<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$supplier_typecode = trim($_POST['supplier_typecode']);
	$supplier_typename = trim($_POST['supplier_typename']);
	if($action == "add"){
		$sql = "INSERT INTO `db_supplier_type` (`supplier_typeid`,`supplier_typecode`,`supplier_typename`,`supplier_typestatus`) VALUES (NULL,'$supplier_typecode','$supplier_typename',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:supplier_type.php");
		}
	}elseif($action == "edit"){
		$supplier_typeid = $_POST['supplier_typeid'];
		$supplier_typestatus = $_POST['supplier_typestatus'];
		$sql = "UPDATE `db_supplier_type` SET `supplier_typecode` = '$supplier_typecode',`supplier_typename` = '$supplier_typename',`supplier_typestatus` = '$supplier_typestatus'  WHERE `supplier_typeid` = '$supplier_typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$supplier_typeid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_supplier_type` WHERE `supplier_typeid` IN ($supplier_typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>