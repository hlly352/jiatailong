<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$orderid = $_POST['orderid'];
	$listid = $_POST['listid'];
	$unit_price = $_POST['unit_price'];
	$tax_rate = $_POST['tax_rate'];
	$iscash = $_POST['iscash'];
	$plan_date = $_POST['plan_date'];
	$remark = trim($_POST['remark']);
	$sql = "UPDATE `db_cutter_order_list` SET `unit_price` = '$unit_price',`tax_rate` = '$tax_rate',`iscash` = '$iscash',`plan_date` = '$plan_date',`remark` = '$remark' WHERE `listid` = '$listid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:cutter_order_list.php?orderid=".$orderid);
	}
}
?>