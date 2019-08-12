<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$supplierid = $_POST['supplierid'];
	$order_date = $_POST['order_date'];
	$delivery_cycle = $_POST['delivery_cycle'];
	$pay_type = $_POST['pay_type'];
	if($action == "add"){
		//自动生成编号
		$sql_number = "SELECT MAX((SUBSTRING(`order_number`,-2)+0)) AS `max_number` FROM `db_material_order` WHERE DATE_FORMAT(`order_date`,'%Y-%m-%d') = '$order_date'";
		$result_number = $db->query($sql_number);
		if($result_number->num_rows){
			$array_number = $result_number->fetch_assoc();
			$max_number = $array_number['max_number'];
			$next_number = $max_number + 1;
			$order_number = date('Ymd',strtotime($order_date)).strtolen($next_number,2).$next_number;
		}else{
			$order_number = date('Ymd',strtotime($order_date))."01";
		} 
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
		$sql = "INSERT INTO `db_material_order` (`orderid`,`order_number`,`order_date`,`delivery_cycle`,`supplierid`,`employeeid`,`dotime`,`order_status`,`pay_type`) VALUES (NULL,'$order_number','$order_date','$delivery_cycle','$supplierid','$employeeid','$dotime',0,'$pay_type')";
		$db->query($sql);
		if($orderid = $db->insert_id){
			header('location:material_order_list_add.php?id='.$orderid);
		}
	}elseif($action == "edit"){
		$orderid = $_POST['orderid'];
		$order_status = $_POST['order_status'];
		//把每一项金额汇总到订单汇总表中
		if($order_status == 1){
			$amount_sql = "SELECT SUM(`actual_quantity` * `unit_price`) FROM `db_material_order_list` WHERE `orderid` = '$orderid'";
			$result_amount = $db->query($amount_sql);
			if($result_amount->num_rows){
				$amounts = $result_amount->fetch_row()[0];
			}
			$order_amount_sql = "UPDATE `db_material_order` SET `order_amount` = '$amounts' WHERE `orderid` = '$orderid'";
		}else{
			$order_amount_sql = "UPDATE `db_material_order` SET `order_amount` = 0 WHERE `orderid` = '$orderid'";
		}
		$db->query($order_amount_sql);

		//更改订单状态
		$sql = "UPDATE `db_material_order` SET `delivery_cycle` = '$delivery_cycle',`supplierid` = '$supplierid',`order_status` = '$order_status' WHERE `orderid` = '$orderid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$orderid = fun_convert_checkbox($array_id);
		$sql_order_list = "DELETE FROM `db_material_order_list` WHERE `orderid` IN ($orderid)";
		$db->query($sql_order_list);
		$sql = "DELETE FROM `db_material_order` WHERE `orderid` IN ($orderid)";
		$db->query($sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}
}
?>