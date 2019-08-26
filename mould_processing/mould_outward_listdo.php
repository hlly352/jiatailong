<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';

if($_POST['submit']){
	$orderid = $_POST['orderid'];
	$array_materialid = $_POST['materialid'];
	$array_order_quantity = $_POST['order_quantity'];
	$array_unit_price = $_POST['unit_price'];
	$array_amount = $_POST['amount'];
	foreach($array_materialid as $key=>$materialid){
		$order_quantity = $array_order_quantity[$key];
		$unit_price = $array_unit_price[$key];
		$amount = $array_amount[$key];
		if($order_quantity && $unit_price){
			$sql_list .= "(NULL,'$orderid','$materialid','$order_quantity','$unit_price','$amount'),";
		}
	}
	$sql_list = rtrim($sql_list,',');
	$sql = "INSERT INTO `db_outward_order_list` (`listid`,`orderid`,`materialid`,`order_quantity`,`unit_price`,`amount`) VALUES $sql_list";
	
	$db->query($sql);
	if($db->insert_id){
		header("location:outward_order_list.php?id=".$orderid);
	}
}
?>