<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$orderid = $_POST['orderid'];
	$array_actual_quantity = $_POST['actual_quantity'];
	$array_unit_price = $_POST['unit_price'];
	$array_tax_rate = $_POST['tax_rate'];
	$array_amount = $_POST['amount'];
	$array_iscash = $_POST['iscash'];
	$array_plan_date = $_POST['plan_date'];
	$array_remark = $_POST['remark'];
	$array_materialid = $_POST['materialid'];
	foreach($array_materialid as $key=>$materialid){
		$actual_quantity = $array_actual_quantity[$key];
		$unit_price = $array_unit_price[$key];
		$tax_rate = $array_tax_rate[$key];
		$amount = $array_amount[$key];
		$iscash = $array_iscash[$key];
		$plan_date = $array_plan_date[$key];
		$remark = trim($array_remark[$key]);
		$materialid = $array_materialid[$key];
		if($actual_quantity && $unit_price){
			$sql_list .= "(NULL,'$orderid','$materialid','$actual_quantity','$unit_price','$tax_rate','$amount','$iscash','$plan_date','$remark'),";
			$enquiry_materialid .= $materialid.',';
		}
	}
	$sql_list = rtrim($sql_list,',');
	$enquiry_materialid = rtrim($enquiry_materialid,',');
	$sql = "INSERT INTO `db_other_material_orderlist` (`listid`,`orderid`,`materialid`,`actual_quantity`,`unit_price`,`tax_rate`,`amount`,`iscash`,`plan_date`,`remark`) VALUES $sql_list";
	$db->query($sql);
	if($db->insert_id){
		$sql_inquiry = "UPDATE `db_mould_other_material` SET `status`='E' WHERE `mould_other_id` IN ($enquiry_materialid)";
		
		$db->query($sql_inquiry);
		header("location:other_material_orderlist.php?id=".$orderid);
	}
}
?>