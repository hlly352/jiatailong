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
		$sql = "INSERT INTO `db_material_order` (`orderid`,`order_number`,`order_date`,`delivery_cycle`,`supplierid`,`employeeid`,`dotime`,`order_status`) VALUES (NULL,'$order_number','$order_date','$delivery_cycle','$supplierid','$employeeid','$dotime',0)";
		$db->query($sql);
		if($orderid = $db->insert_id){
			header('location:material_order_list_add.php?id='.$orderid);
		}
	}elseif($action == "edit"){
		$orderid = $_POST['orderid'];
		$order_status = $_POST['order_status'];
		$sql = "UPDATE `db_material_order` SET `delivery_cycle` = '$delivery_cycle',`supplierid` = '$supplierid',`order_status` = '$order_status' WHERE `orderid` = '$orderid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		//通过计划id查询对应的详情
		$planid_array = $_POST['id'];
		foreach($planid_array as $k=>$v){
			$list_sql = "SELECT `accountid`,`plan_amount` FROM `db_funds_plan_list` WHERE `planid` ='$v'";
			$result_list = $db->query($list_sql);
			if($result_list->num_rows){
				while($row_list = $result_list->fetch_assoc()){
					//把对账表中的计划付款金额还原
					$plan_amount = $row_list['plan_amount'];
					$account_sql = "UPDATE `db_material_account` SET `status` = 'F',`apply_amount` = `apply_amount` - $plan_amount WHERE `accountid` = ".$row_list['accountid'];
					$db->query($account_sql);
					if($db->affected_rows){
						//付款金额小于零则重置为零
						$update_sql = "UPDATE `db_material_account` SET `apply_amount` = 0 WHERE `apply_amount` <0";
						$db->query($update_sql);
						$array_id = $_POST['id'];
						$planid = fun_convert_checkbox($array_id);
						$plan_sql = "DELETE FROM `db_material_funds_plan` WHERE `planid` IN ($planid)";
						$db->query($plan_sql);
						$list_sql = "DELETE FROM `db_funds_plan_list` WHERE `planid` IN ($planid)";
						$db->query($list_sql);
						if($db->affected_rows){
							header('location:'.$_SERVER['HTTP_REFERER']);
						}
					}
				}
			}else{
				$del_sql = "DELETE FROM `db_material_funds_plan` WHERE `planid` = '$v'";
				$db->query($del_sql);
				if($db->affected_rows){
					header('location:'.$_SERVER['HTTP_REFERER']);
				}
			}

		}
	}
}
?>