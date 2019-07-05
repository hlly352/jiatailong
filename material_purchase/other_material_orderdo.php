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
	if($action == "add"){
		//自动生成编号
		$sql_number = "SELECT MAX((SUBSTRING(`order_number`,-2)+0)) AS `max_number` FROM `db_other_material_order` WHERE DATE_FORMAT(`order_date`,'%Y-%m-%d') = '$order_date'";
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
		$dotime = time();
		$sql = "INSERT INTO `db_other_material_order` (`orderid`,`order_number`,`order_date`,`delivery_cycle`,`supplierid`,`employeeid`,`dotime`,`order_status`) VALUES (NULL,'$order_number','$order_date','$delivery_cycle','$supplierid','$employeeid','$dotime',0)";
		
		$db->query($sql);
		if($orderid = $db->insert_id){
			header('location:other_material_order_add.php?id='.$orderid);
		}
	}elseif($action == "edit"){
		$orderid = $_POST['orderid'];
		$order_status = $_POST['order_status'];
		$sql = "UPDATE `db_other_material_order` SET `delivery_cycle` = '$delivery_cycle',`supplierid` = '$supplierid',`order_status` = '$order_status' WHERE `orderid` = '$orderid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:other_material_order.php');
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$orderid = fun_convert_checkbox($array_id);
		// 把物料退回通过状态
		$material_sql = "SELECT `materialid` FROM `db_other_material_orderlist` WHERE `orderid` IN ($orderid)";
		$res_material = $db->query($material_sql);
		if($res_material->num_rows){
			while($row_material = $res_material->fetch_assoc()){
				$status_sql = "UPDATE `db_mould_other_material` SET `status`='B' WHERE `mould_other_id` =".$row_material['materialid'];
				$db->query($status_sql);
			}
		}
		$sql_order_list = "DELETE FROM `db_other_material_orderlist` WHERE `orderid` IN ($orderid)";
		$db->query($sql_order_list);
		$sql = "DELETE FROM `db_other_material_order` WHERE `orderid` IN ($orderid)";
		$db->query($sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}
}
?>