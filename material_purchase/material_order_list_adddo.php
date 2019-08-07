<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$orderid = $_POST['orderid'];
	$array_materialid = $_POST['materialid'];
	$array_order_quantity = $_POST['order_quantity'];
	$array_actual_quantity = $_POST['actual_quantity'];
	$array_unitid = $_POST['unitid'];
	$array_actual_unitid = $_POST['actual_unitid'];
	$array_unit_price = $_POST['unit_price'];
	$array_tax_rate = $_POST['tax_rate'];
	$array_process_cost = $_POST['process_cost'];
	$array_iscash = $_POST['iscash'];
	$array_plan_date = $_POST['plan_date'];
	$array_remark = $_POST['remark'];
	foreach($array_materialid as $key=>$materialid){
		$order_quantity = $array_order_quantity[$key];
		$actual_quantity = $array_actual_quantity[$key];
		$unitid = $array_unitid[$key];
		$actual_unitid = $array_actual_unitid[$key];
		$unit_price = $array_unit_price[$key];
		$tax_rate = $array_tax_rate[$key];
		$process_cost = $array_process_cost[$key]; 
		$iscash = $array_iscash[$key];
		$plan_date = $array_plan_date[$key];
		$remark = trim($array_remark[$key]);
		if($actual_quantity && $unit_price){
			$sql_list .= "(NULL,'$orderid','$materialid','$order_quantity','$actual_quantity',0,0,'$unitid','$actual_unitid','$unit_price','$tax_rate','$process_cost','$iscash','$plan_date','$remark'),";
			$enquiry_materialid .= $materialid.',';
		}
	}
	$sql_list = rtrim($sql_list,',');
	$enquiry_materialid = rtrim($enquiry_materialid,',');
	$sql = "INSERT INTO `db_material_order_list` (`listid`,`orderid`,`materialid`,`order_quantity`,`actual_quantity`,`in_quantity`,`order_surplus`,`unitid`,`actual_unitid`,`unit_price`,`tax_rate`,`process_cost`,`iscash`,`plan_date`,`remark`) VALUES $sql_list";
	
	$db->query($sql);
	if($db->insert_id){
		header("location:material_order_list.php?id=".$orderid);
	}
	$sql_inquiry = "DELETE FROM `db_material_inquiry` WHERE `materialid` IN ($enquiry_materialid)";
	$db->query($sql_inquiry);
}
?>