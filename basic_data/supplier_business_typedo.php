<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$business_typename = trim($_POST['business_typename']);
	if($action == "add"){
		$sql = "INSERT INTO `db_supplier_business_type` (`business_typeid`,`business_typename`,`business_typestatus`) VALUES (NULL,'$business_typename',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:supplier_business_type.php");
		}
	}elseif($action == "edit"){
		$business_typeid = $_POST['business_typeid'];
		$business_typestatus = $_POST['business_typestatus'];
		$sql = "UPDATE `db_supplier_business_type` SET `business_typename` = '$business_typename',`business_typestatus` = '$business_typestatus'  WHERE `business_typeid` = '$business_typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$business_typeid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_supplier_business_type` WHERE `business_typeid` IN ($business_typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>