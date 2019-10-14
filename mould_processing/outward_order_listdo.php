<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];

if($_POST['submit']){
	$orderid = $_POST['orderid'];
	$inquiry_orderid = $_POST['inquiry_orderid'];
	$array_inquiryid = $_POST['inquiryid'];
	$array_unit_price = $_POST['unit_price'];
	$sql_str = '';
	foreach($array_inquiryid as $key=>$inquiryid){
		$unit_price = $array_unit_price[$key];
		if($unit_price && $inquiryid){
			$sql_str .= '(\''.$orderid.'\',\''.$inquiryid.'\',\''.$unit_price.'\',\''.$employeeid.'\'),';
		}
	}
	$sql_str = rtrim($sql_str,',');
	//更改订单表中的询价单信息
	$sql_order = "UPDATE `db_outward_order` SET `inquiry_orderid` = '$inquiry_orderid' WHERE `orderid` = '$orderid'";
	$db->query($sql_order);
	//添加到外协订单中
	$sql_orderlist = "INSERT INTO `db_outward_order_list`(`orderid`,`inquiryid`,`unit_price`,`employeeid`) VALUES $sql_str";
	echo $sql_orderlist;
	$db->query($sql_orderlist);
	if($db->insert_id){
		header("location:outward_order_list.php?id=".$orderid);
	}
}
