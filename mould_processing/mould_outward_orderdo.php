<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = $_REQUEST['action'];
if($action == 'add'){
	$order_date = date('Y-m-d');
	$dotime = fun_gettime();
	$sql_number = "SELECT MAX((SUBSTRING(`order_number`,-2)) +0) AS `max_number` FROM `db_outward_order` WHERE DATE_FORMAT(`order_date`,'%Y-%m-%d') = '$order_date'";
	$result_number = $db->query($sql_number);
	if($result_number->num_rows){
		$array_number = $result_number->fetch_assoc();
		$max_number = $array_number['max_number'];
		$next_number = $max_number + 1;
		$order_number = date('Ymd',strtotime($order_date)).strtolen($next_number,2).$next_number;
	}else{
		$order_number = date('Ymd',strtotime($order_date)).'01';
	}

	$sql = "INSERT INTO `db_outward_order` (`orderid`,`order_number`,`order_date`,`inquiry_orderid`,`employeeid`,`dotime`,`order_status`) VALUES (NULL,'$order_number','$order_date','0','$employeeid','$dotime','0')";
		$db->query($sql);
		if($orderid = $db->insert_id){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
}
if($_POST['submit']){
	$supplierid = $_POST['supplierid'];
	$inquiry_date = $_POST['order_date'];
	$delivery_cycle = $_POST['delivery_cycle'];
	$workteamid = $_POST['workteamid'];
	if($action == "add"){
		//自动生成编号
		$sql_number = "SELECT MAX((SUBSTRING(`inquiry_number`,-2)+0)) AS `max_number` FROM `db_outward_inquiry_order` WHERE DATE_FORMAT(`inquiry_date`,'%Y-%m-%d') = '$inquiry_date'";
		$result_number = $db->query($sql_number);
		if($result_number->num_rows){
			$array_number = $result_number->fetch_assoc();
			$max_number = $array_number['max_number'];
			$next_number = $max_number + 1;
			$inquiry_number = date('Ymd',strtotime($inquiry_date)).strtolen($next_number,2).$next_number;
		}else{
			$inquiry_number = date('Ymd',strtotime($inquiry_date))."01";
		} 
		$employeeid = $_SESSION['employee_info']['employeeid'];
		
		$sql = "INSERT INTO `db_outward_inquiry_order` (`inquiry_orderid`,`inquiry_number`,`inquiry_date`,`delivery_cycle`,`supplierid`,`employeeid`,`dotime`,`inquiry_status`,`workteamid`) VALUES (NULL,'$inquiry_number','$inquiry_date','$delivery_cycle','$supplierid','$employeeid','$dotime','0','$workteamid')";
		$db->query($sql);
		if($inquiryid = $db->insert_id){
			header('location:outward_inquiry_order_list_add.php?id='.$inquiryid);
		}
	}elseif($action == "edit"){
		$orderid = $_POST['orderid'];
		$supplierid = $_POST['supplierid'];
		$order_date = $_POST['order_date'];
		$delivery_cycle = $_POST['delivery_cycle'];
		$outward_type = $_POST['outward_type'];
		$workteamid = $_POST['workteamid'];
		$applyer = trim($_POST['applyer']);
		$order_status = $_POST['order_status'];
		//更改订单状态
		$sql = "UPDATE `db_outward_order` SET `delivery_cycle` = '$delivery_cycle',`supplierid` = '$supplierid',`order_status` = '$order_status',`outward_typeid`='$outward_type',`workteamid` = '$workteamid',`applyer` = '$applyer',`order_date` = '$order_date' WHERE `orderid` = '$orderid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:mould_outward_order.php');
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$orderid = fun_convert_checkbox($array_id);
		$sql_order_list = "DELETE FROM `db_outward_order_list` WHERE `orderid` IN ($orderid)";
		$db->query($sql_order_list);
		$sql = "DELETE FROM `db_outward_order` WHERE `orderid` IN ($orderid)";
		$db->query($sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == 'del_list'){
		$array_inquiryid = $_POST['id'];
		$inquiryid = fun_convert_checkbox($array_inquiryid);
		$sql_list = "DELETE FROM `db_outward_order_list` WHERE `inquiryid` IN ($inquiryid)";
		$db->query($sql_list);
		$sql_inquiry = "DELETE FROM `db_outward_inquiry_orderlist` WHERE `inquiryid` IN($inquiryid)";
		$db->query($sql_inquiry);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}
}
?>