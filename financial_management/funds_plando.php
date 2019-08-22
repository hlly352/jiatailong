<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = $_REQUEST['action'];
$planid = $_REQUEST['planid'];

if($action == 'pay'){
			$data_source = $_POST['data_source'];
			$accountid = $_POST['accountid'];
			if( $data_source == 'B'){
			//接收数据
			$listid_array = $_POST['id'];
			//遍历对账id查找对应的信息
			$i = 0;
			foreach($listid_array as $v){
				$k = 'plan_amount_'.$v;
				$plan_amount = $_POST[$k];
				//查找计划详情中是否存在
				// $plan_sql = "SELECT `listid` FROM `db_funds_plan_payment` WHERE `plan_listid` = '$v'";
				// $result_plan = $db->query($plan_sql);
				// if($result_plan->num_rows){
				// 	$listid = $result_plan->fetch_assoc()['listid'];
				// 	//累加到原来的计划中
				// 	$plan_payment_sql = "UPDATE `db_funds_plan_payment` SET `payment` = `payment` + '$plan_amount' WHERE `listid` = '$listid'";
				// 	$db->query($plan_payment_sql);
				// }else{
					//对账订单信息插入到计划详情表中
					$list_sql = "INSERT INTO `db_funds_plan_payment`(`plan_listid`,`payment`) VALUES ('$v','$plan_amount') ";
	
					$db->query($list_sql);
				// }
				//支付金额插入到账款管理表中
				$funds_sql = "UPDATE `db_material_funds_list` SET `apply_amount` = `apply_amount` + '$plan_amount' WHERE `accountid` = '$accountid'";
				$db->query($funds_sql);
				//把计划金额累计到对账详情表中的订单中
				$plan_sql = "UPDATE `db_funds_plan_list` SET `tot_payment` = `tot_payment` + '$plan_amount' WHERE `listid` = '$v'";
				$db->query($plan_sql);
				//如果支付完成则更改状态
				$plan_status_sql = "UPDATE `db_funds_plan_list` SET `plan_status` = 'F' WHERE `plan_amount` = `tot_payment` AND `listid` = '$v'";
			
				$db->query($plan_status_sql);
				//如果支付完成则更改计划汇总表状态
				$status_sql = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` IN('A','B','C','D','E') AND `planid` = (SELECT `planid` FROM `db_funds_plan_list` WHERE `listid` = '$v')";
				$result_status = $db ->query($status_sql);
				$count_status = $result_status->num_rows;
				if($count_status == 0){
				$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '15' WHERE `planid` =(SELECT `planid` FROM `db_funds_plan_list` WHERE `listid` = '$v')";
				
				$db->query($sql);
			}
			}
			
			
			
				header('location:'.$_SERVER['HTTP_REFERER']);
			
	 	 }else{
	  	//单个订单添加
	  	//接收数据
	  	$data = $_GET;
	  	$plan_amount = $data['plan_amount'];
	  	$plan_listid = $data['plan_listid'];
	  	$accountid = $data['accountid'];
	  	//查询是否存在计划信息
	  	// $plan_sql = "SELECT `listid` FROM `db_funds_plan_payment` WHERE `plan_listid` = '$plan_listid'";
	  	// $result_plan = $db->query($plan_sql);
	  	// if($result_plan->num_rows){
	  	// 	$listid = $result_plan ->fetch_assoc()['listid'];
	  	// 	//加入金额
	  	// 	$plan_payment_sql = "UPDATE `db_funds_plan_payment` SET `payment` = `payment` + '$plan_amount' WHERE `listid` = '$listid'";
	  	// 	$db->query($plan_payment_sql);
	  	// }else{
	  	//单条计划信息插入到计划详情中
	  	$list_sql = "INSERT INTO `db_funds_plan_payment`(`plan_listid`,`payment`) VALUES('$plan_listid','$plan_amount')";

	  	$db->query($list_sql);
	  	//付款金额插入到账款管理中
	  	$funds_sql = "UPDATE `db_material_funds_list` SET `apply_amount` = `apply_amount` + '$plan_amount' WHERE `accountid` = '$accountid'";
	  	$db->query($funds_sql);
	  		// }
	  		//把计划金额累计到对账详情表中的订单中
			$order_sql = "UPDATE `db_funds_plan_list` SET `tot_payment` = `tot_payment` + '$plan_amount' WHERE `listid` = '$plan_listid'";
			$db->query($order_sql);
			//如果支付完成则更改状态
			$plan_sql = "UPDATE `db_funds_plan_list` SET `plan_status` = 'F' WHERE `plan_amount` = `tot_payment` AND `listid` = '$plan_listid'";
			
			$db->query($plan_sql);
			//如果支付完成则更改计划汇总表状态
			$plan_status_sql = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` IN('A','B','C','D','E') AND `planid` = (SELECT `planid` FROM `db_funds_plan_list` WHERE `listid` = '$plan_listid')";
			$result_plan_status = $db ->query($plan_status_sql);
			$count_plan_status = $result_plan_status->num_rows;
			if($count_plan_status == 0){
				$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '15' WHERE `planid` =(SELECT `planid` FROM `db_funds_plan_list` WHERE `listid` = '$plan_listid')";
				
				$db->query($sql);
			}
			
	  		header('location:'.$_SERVER['HTTP_REFERER']);
	  	

	  }
}elseif($action == 'querys'){ 
	//更改支付状态
	$listid = $_GET['payment_listid'];
	$planid = $_GET['planid'];
	$pay_status_sql = "UPDATE `db_funds_plan_payment` SET `pay_status` = 'C' WHERE `listid` = '$listid'";

	$db->query($pay_status_sql);
	//更改确认金额
	$query_sql = "UPDATE `db_funds_plan_list` SET `query_amount` = `query_amount` + (SELECT `payment` FROM `db_funds_plan_payment` WHERE `listid` = '$listid') WHERE `listid` = (SELECT `plan_listid` FROM `db_funds_plan_payment` WHERE `listid` = '$listid')";
	$db->query($query_sql);
	//确认金额等于计划金额则更改状态
	$plan_sql = "UPDATE `db_funds_plan_list` SET `plan_status` = 'G' WHERE `plan_amount` = `query_amount` AND listid =(SELECT `plan_listid` FROM `db_funds_plan_payment` WHERE `listid` = '$listid') ";
	$db->query($plan_sql);
	//全部确认则更改计划状态
	$status_sql = "SELECT * FROM `db_funds_plan_list` WHERE `planid` = '$planid' AND `plan_status` IN('A','B','C','D','E')";
	$result_status = $db->query($status_sql);
	$count_status = $result_status->num_rows;
	if($count_status == 0){
		$sql = "UPDATE `db_material_funds_plan` SET `plan_status` ='17' WHERE `planid` = '$planid'";
		$db->query($sql);
	}
	header('location:'.$_SERVER['HTTP_REFERER']);	
}else{
		//更改计划状态
		if($action == 'complete'){
			$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '3' WHERE `planid` = '$planid'";
			$db->query($sql);
			header('location:material_funds_plan.php');
		}elseif($action == 'back'){
			$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '0' WHERE `planid` = '$planid'";
			$db->query($sql);
			header('location:material_funds_plan.php');
		}
	}
?>