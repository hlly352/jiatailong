<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_purchaseid = fun_convert_checkbox($_POST['id']);
	$sql_list = "DELETE FROM `db_cutter_purchase_list` WHERE `purchaseid` IN ($array_purchaseid)";
	$db->query($sql_list);
	$sql = "DELETE FROM `db_cutter_purchase` WHERE `purchaseid` IN ($array_purchaseid)";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>