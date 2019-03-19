<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	if($action == "add"){
		$item_type_sn = trim($_POST['item_type_sn']);
		$item_typename = trim($_POST['item_typename']);
		$sql = "INSERT INTO `db_quote_item_type` (`item_typeid`,`item_type_sn`,`item_typename`) VALUES (NULL,'$item_type_sn','$item_typename')";
		$db->query($sql);
		if($db->insert_id){
			header("location:item_type.php");
		}
	}elseif($action == "edit"){
		$item_type_sn = trim($_POST['item_type_sn']);
		$item_typename = trim($_POST['item_typename']);
		$item_typeid = trim($_POST['item_typeid']);
		$sql = "UPDATE `db_quote_item_type` SET `item_type_sn` = '$item_type_sn',`item_typename` = '$item_typename' WHERE `item_typeid` = '$item_typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}
	}elseif($action == "del"){
		$array_item_typeid = fun_convert_checkbox($_POST['id']);
		$sql = "DELETE FROM `db_quote_item_type` WHERE `item_typeid` IN ($array_item_typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>