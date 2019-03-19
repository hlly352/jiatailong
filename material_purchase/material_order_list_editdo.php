<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$listid = $_POST['listid'];
	$order_quantity = $_POST['order_quantity'];
	$actual_quantity = $_POST['actual_quantity'];
	$unit_price = $_POST['unit_price'];
	$unitid = $_POST['unitid'];
	$actual_unitid = $_POST['actual_unitid'];
	$tax_rate = $_POST['tax_rate'];
	$process_cost = $_POST['process_cost'];
	$iscash = $_POST['iscash'];
	$plan_date = $_POST['plan_date'];
	$remark = trim($_POST['remark']);
	$sql = "UPDATE `db_material_order_list` SET `order_quantity` = '$order_quantity',`actual_quantity` = '$actual_quantity',`in_quantity` = 0,`order_surplus` = 0,`unit_price` = '$unit_price',`unitid` = '$unitid',`actual_unitid` = '$actual_unitid',`tax_rate` = '$tax_rate',`process_cost` = '$process_cost',`iscash` = '$iscash',`plan_date` = '$plan_date',`remark` = '$remark' WHERE `listid` = '$listid'";
	$db->query($sql);
	if($db->affected_rows){
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
}
?>