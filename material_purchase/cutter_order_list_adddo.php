<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$orderid = $_POST['orderid'];
	$array_purchase_listid = $_POST['purchase_listid'];
	$array_unit_price = $_POST['unit_price'];
	$array_tax_rate = $_POST['tax_rate'];
	$array_iscash = $_POST['iscash'];
	$array_plan_date = $_POST['plan_date'];
	$array_remark = $_POST['remark'];
	foreach($array_purchase_listid as $key=>$purchase_listid){
		$unit_price = $array_unit_price[$key];
		$tax_rate = $array_tax_rate[$key];
		$iscash = $array_iscash[$key];
		$plan_date = $array_plan_date[$key];
		$remark = trim($array_remark[$key]);
		if($unit_price){
			$sql_list .= "(NULL,'$orderid','$purchase_listid','$unit_price','$tax_rate','$iscash','$plan_date','$remark'),";
			$inquiry_purchase_listid .= $purchase_listid.',';
		}
	}
	$sql_list = rtrim($sql_list,',');
	$inquiry_purchase_listid = rtrim($inquiry_purchase_listid,',');
	$sql = "INSERT INTO `db_cutter_order_list` (`listid`,`orderid`,`purchase_listid`,`unit_price`,`tax_rate`,`iscash`,`plan_date`,`remark`) VALUES $sql_list";
	$db->query($sql);
	if($db->insert_id){
		header("location:cutter_order_list.php?orderid=".$orderid);
	}
	$sql_inquiry = "DELETE FROM `db_cutter_inquiry` WHERE `purchase_listid` IN ($inquiry_purchase_listid)";
	$db->query($sql_inquiry);
}
?>