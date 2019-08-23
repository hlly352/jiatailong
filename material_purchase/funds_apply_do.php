<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
	$action = $_REQUEST['action'];
	$from = $_GET['from'];
	$planid = $_GET['planid'];
	$accountid = $_GET['accountid'];
	$order_sql = "SELECT `db_funds_plan_list`.`listid` FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid`  INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_account_order_list`.`orderid` WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_account_order_list`.`accountid` = '$accountid'";
	$result_order = $db->query($order_sql);
	if($result_order->num_rows){
		while($row_order = $result_order->fetch_assoc()){
	 		$listid_array[] = $row_order['listid'];
		 	}
		 }
	$listid = fun_convert_checkbox($listid_array);
	if($from == 'purchase'){
		if($action == 'complete'){
			 //更改计划详情的状态
			 $sql = "UPDATE `db_funds_plan_list` SET `plan_status` = 'C' WHERE `listid` IN($listid)";
			 $db->query($sql);
			 //判断当前计划是否完全通过审核
			 $plan_sql = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status`='B' OR `plan_status` = 'A' AND `planid` = '$planid'";
			 $result_plan= $db->query($plan_sql);
			 $count = $result_plan->num_rows;
			
			 if($count == 0){
			 	//更改计划状态
			 	$status_sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '9' WHERE `planid`='$planid'";
			 	$db->query($status_sql);
			 	header('location:material_funds_plan.php');
			 }else{
			 	header('location:funds_pay_apply.php?action=purchase&id='.$planid);
			 }
		}elseif($action == 'back'){
			//撤回审核
			$sql = "UPDATE `db_funds_plan_list` SET `plan_status` = 'A' WHERE `listid` IN($listid)";
			$db->query($sql);
			//判断是不是完全撤回
			 $plan_sql = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` IN('B','C','D','E','F','G') AND `planid` = '$planid'";
			 $result_plan= $db->query($plan_sql);
			 $count = $result_plan->num_rows;
			if($count == 0){
				//更改计划状态
			 	$status_sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '5' WHERE `planid`='$planid'";
			 	$db->query($status_sql);
			 	header('location:material_funds_plan.php');
			}else{
				header('location:material_funds_plan.php');
			}
			
			
			
		}
	}elseif($from == 'boss'){
		if($action == 'complete'){
			 //更改计划详情的状态
			 $sql = "UPDATE `db_funds_plan_list` SET `plan_status` = 'E' WHERE `listid` IN($listid)";
			 $db->query($sql);
			 //判断当前计划是否完全通过审核
			 $plan_sql = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` IN('A','B','C','D') AND `planid` = '$planid'";
			 $result_plan= $db->query($plan_sql);
			 $count = $result_plan->num_rows;
			
			 if($count == 0){
			 	//更改计划状态
			 	$status_sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '13' WHERE `planid`='$planid'";
			 	$db->query($status_sql);
			 	header('location:material_funds_plan.php');
			 }else{
			 	header('location:funds_pay_apply.php?action=purchase&id='.$planid);
			 }
		}elseif($action == 'back'){
			//撤回审核
			$sql = "UPDATE `db_funds_plan_list` SET `plan_status` = 'C' WHERE `listid` IN($listid)";
			$db->query($sql);
			
			header('location:material_funds_plan.php');
			
			
			
		}
	}
			//header('location:material_funds_plan.php');
		
		
	
	