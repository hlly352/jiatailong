<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	if($action == "add" || $action == "edit"){
		$item_typeid = $_POST['item_typeid'];
		$item_sn = trim($_POST['item_sn']);
		$item_name = trim($_POST['item_name']);
		$specification = trim($_POST['specification']);
		$unit_price = $_POST['unit_price'];
		$descripition = trim($_POST['descripition']);
	}
	if($action == "add"){
		$sql = "INSERT INTO `db_quote_item` (`itemid`,`item_sn`,`item_name`,`specification`,`unit_price`,`descripition`,`item_typeid`) VALUES (NULL,'$item_sn','$item_name','$specification','$unit_price','$descripition','$item_typeid')";
		$db->query($sql);
		if($db->insert_id){
			header("location:quote_item.php");
		}
	}elseif($action == "edit"){
		$itemid = trim($_POST['itemid']);
		$sql = "UPDATE `db_quote_item` SET `item_sn` = '$item_sn',`item_name` = '$item_name',`specification` = '$specification',`unit_price` = '$unit_price',`descripition` = '$descripition',`item_typeid` = '$item_typeid' WHERE `itemid` = '$itemid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}
	}elseif($action == "del"){
		$array_itemid = fun_convert_checkbox($_POST['id']);
		$sql = "DELETE FROM `db_quote_item` WHERE `itemid` IN ($array_itemid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>