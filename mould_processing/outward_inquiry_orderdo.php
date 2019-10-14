<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_POST['submit']){
	$action = $_POST['action'];
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
		$dotime = fun_gettime();
		$sql = "INSERT INTO `db_outward_inquiry_order` (`inquiry_orderid`,`inquiry_number`,`inquiry_date`,`delivery_cycle`,`supplierid`,`employeeid`,`dotime`,`inquiry_order_status`,`workteamid`) VALUES (NULL,'$inquiry_number','$inquiry_date','$delivery_cycle','$supplierid','$employeeid','$dotime','0','$workteamid')";
		$db->query($sql);
		if($inquiryid = $db->insert_id){
			header('location:outward_inquiry_order_list_add.php?id='.$inquiryid);
		}
	}elseif($action == "edit"){
		$inquiry_orderid = $_POST['inquiry_orderid'];
		$supplierid = $_POST['supplierid'];
		$inquiry_date = $_POST['inquiry_date'];
		$workteamid = $_POST['workteamid'];
		$order_status = $_POST['order_status'];
		//更改订单状态
		$sql = "UPDATE `db_outward_inquiry_order` SET `supplierid` = '$supplierid',`inquiry_order_status` = '$order_status',`workteamid` = '$workteamid',`inquiry_date` = '$inquiry_date' WHERE `inquiry_orderid` = '$inquiry_orderid'";

		$db->query($sql);
			header('location:outward_inquiry_order.php');
	}elseif($action == 'approval'){

		$unit_price = $_POST['unit_price'];
		$listid = $_POST['listid'];
		$orderid = $_POST['orderid'];
		$sql = "UPDATE `db_outward_order_list` SET `unit_price` = '$unit_price' WHERE `listid` = '$listid'";
		$db->query($sql);
		header('location:outward_order_list.php?id='.$orderid);
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$inquiry_orderid = fun_convert_checkbox($array_id);
		$sql_order_list = "DELETE FROM `db_outward_inquiry_orderlist` WHERE `inquiry_orderid` IN ($inquiry_orderid)";
		$db->query($sql_order_list);
		$sql = "DELETE FROM `db_outward_inquiry_order` WHERE `inquiry_orderid` IN ($inquiry_orderid)";
		$db->query($sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == 'del_list'){
		$array_listid = $_POST['id'];
		$listid = fun_convert_checkbox($array_listid);
		$sql_list = "DELETE FROM `db_outward_inquiry_orderlist` WHERE `listid` IN ($listid)";
		$db->query($sql_list);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == 'show'){
		$listid = trim($_POST['listid']);
		$back_date = trim($_POST['back_date']);
		//查询询价单id
		$sql_inquiry = "SELECT `inquiry_orderid` FROM `db_outward_inquiry_orderlist` WHERE `listid` = '$listid'";
		$result = $db->query($sql_inquiry);
		if($result->num_rows){
			$id = $result->fetch_assoc()['inquiry_orderid'];
		} 
		$sql = "UPDATE `db_outward_inquiry_orderlist` SET `back_date` = '$back_date' WHERE `listid` = '$listid'";
		$db->query($sql);

		header('location:outward_inquiry_orderlist.php?id='.$id);
	}
}else{
	$action = $_GET['action'];
	if($action == 'back_date'){
		$listid = $_GET['listid'];
		$sql = "UPDATE `db_outward_inquiry_orderlist` SET `back_date` = '' WHERE `listid` = '$listid'";
	
		$db->query($sql);
		header('location:'.$_SERVER['HTTP_REFERER']);
	}
}
?>