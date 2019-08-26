<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = $_GET['action'];
if($action == 'querys'){
	$orderid = $_GET['orderid'];
	//更改外协订单状态
	$sql = "UPDATE `db_outward_order` SET `material_control` = 'Y' WHERE `orderid` = '$orderid'";
	$db->query($sql);
	if($db->affected_rows){
		header('location:outward_in_list.php');
	}
}
?>