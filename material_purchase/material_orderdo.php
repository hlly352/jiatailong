<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
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
		//查找是否是预付订单
		$order_sql = "SELECT `pay_type` FROM `db_material_order` WHERE `orderid` = '$orderid'";
		$result_order = $db->query($order_sql);
		if($result_order->num_rows){
			$pay_type = $result_order->fetch_row()[0];
		}
		//预付订单则添加到对账表中
		if($pay_type == 'P'){
			//通过订单状态，插入或者删除对账汇总表中的内容
			if($order_status == 1){
				//查询当前订单金额
				$order_amount_sql = "SELECT `db_material_order`.`supplierid`,SUM(`db_material_order_list`.`actual_quantity` * `db_material_order_list`.`unit_price`) AS `order_amount` FROM `db_material_order` INNER JOIN `db_material_order_list` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` WHERE `db_material_order`.`orderid` = '$orderid' ";
				$result_order_amount = $db->query($order_amount_sql);
				if($result_order_amount){
					$order_array = $result_order_amount->fetch_assoc();
					$order_amount = $order_array['order_amount'];
					$supplierid = $order_array['supplierid'];
					$time = date('Y-m-d');
					//把订单信息插入到对账汇总表中
					$account_sql = "INSERT INTO `db_material_account`(`account_time`,`tot_amount`,`supplierid`,`employeeid`,`orderidlist`,`account_type`,`status`) VALUES(DATE_FORMAT(NOW(),'%Y-%m-%d'),'$order_amount','$supplierid','$employeeid','$orderid','M','Y')";
				
					$db->query($account_sql);
					$accountid = $db->insert_id;
					//插入到对账订单表中
					$order_sql = "INSERT INTO `db_account_order_list`(`accountid`,`orderid`,`order_amount`) VALUES('$accountid','$orderid','$order_amount')";

					$db->query($order_sql);
				}
			}elseif($order_status == 0){
				//查找对账单id
				$accountid_sql = "SELECT `accountid` FROM `db_account_order_list` WHERE `orderid` = '$orderid'";
				$result_accountid = $db->query($accountid_sql);
				if($result_accountid->num_rows){
					$accountid = $result_accountid->fetch_row()[0];
				}
				//删除对账单中的信息
				$account_sql = "DELETE FROM `db_material_account` WHERE `accountid` = '$accountid'";
				$db->query($account_sql);
				$order_sql = "DELETE FROM `db_account_order_list` WHERE `orderid` = '$orderid'";
				$db->query($order_sql);
			}
		}
	

		//更改订单状态
		$sql = "UPDATE `db_material_order` SET `delivery_cycle` = '$delivery_cycle',`supplierid` = '$supplierid',`order_status` = '$order_status' WHERE `orderid` = '$orderid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:material_order.php');
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