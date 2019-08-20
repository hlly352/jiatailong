<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = $_REQUEST['action'];
$plan_date = date('Y-m-d'); 
$data_source = $_POST['data_source'];

	if($action == "add"){
			$planid = $_POST['planid'];
			$accountid = $_POST['accountid'];
			if( $data_source == 'B'){
			//接收数据
			$listid_array = $_POST['id'];
			//遍历对账id查找对应的信息
			$i = 0;
			foreach($listid_array as $v){
				$k = 'plan_amount_'.$v;
				$plan_amount = $_POST[$k];
				//对账订单信息插入到计划详情表中
				$sqllist .= "('$planid','$v','$plan_amount','$employeeid'),";
				//把计划金额累计到对账详情表中的订单中
				$order_sql = "UPDATE `db_account_order_list` SET `plan_amount` = `plan_amount` + '$plan_amount' WHERE `listid` = '$v'";
				$db->query($order_sql);
				//把计划金额加入到对账汇总表中
				$account_sql = "UPDATE `db_material_account` SET `tot_plan_amount` = `tot_plan_amount` + '$plan_amount' WHERE `accountid` = '$accountid'";

				$db->query($account_sql);
			}
			$sqllist = rtrim($sqllist,',');
			$list_sql = "INSERT INTO `db_funds_plan_list`(`planid`,`order_listid`,`plan_amount`,`employeeid`) VALUES $sqllist ";
	
			$db->query($list_sql);
			if($db->affected_rows){
				header('location:'.$_SERVER['HTTP_REFERER']);
			}
	 	 }elseif($data_source == 'C'){
		  	$id_array = $_POST['id'];
		  	$order_amount_array = $_POST['order_amount'];
		  	$plan_amount_array = $_POST['plan_amount'];
		  	
		  	//遍历得到计划金
		  	$i = 0;
		  	foreach($id_array as $key=>$value){
		  		$k1 = 'order_amount_'.$value;
		  		$k2 = 'plan_amount_'.$value;
		  		$k3 = 'supplierid_'.$value;
		  		$k4 = 'account_type_'.$value;

		  		$order_amount = $_POST[$k1];
		  		$plan_amount = $_POST[$k2];
		  		$supplierid = $_POST[$k3];
		  		$account_type = $_POST[$k4];
		  
		  		//更改预付金额
		  		$prepayment_sql = "UPDATE `db_material_order` SET `prepayment` = `prepayment` + '$plan_amount' WHERE `orderid` = '$value'";
		  		$db->query($prepayment_sql);
		  		//插入到计划列表中
		  		$plan_sql = "INSERT INTO `db_funds_plan_list`(`planid`,`orderid`,`order_amount`,`plan_amount`,`supplierid`,`account_type`) VALUES('$planid','$value','$order_amount','$plan_amount','$supplierid','$account_type')";
		  		$db->query($plan_sql);
		  		if(!$db->affected_rows){
		  			$i++;
		  		}
		  	}
		  	if($i == 0){
		  		header('location:'.$_SERVER['HTTP_REFERER']);
		  		}
	  
	  }else{
	  	//单个订单添加
	  	//接收数据
	  	$data = $_GET;
	  	$plan_amount = $data['plan_amount'];
	  	$accountid = $data['accountid'];
	  	unset($data['action']);
	  	unset($data['accountid']);
	  	foreach($data as $k=>$v){
	  		$keylist .= '`'.$k.'`,';
	  		$valuelist .= '"'.$v.'",';
	  	}
	  	$keylist .= '`employeeid`';
	  	$valuelist .= "$employeeid";
	  	//单条计划信息插入到计划详情中
	  	$list_sql = "INSERT INTO `db_funds_plan_list`($keylist) VALUES($valuelist)";

	  	$db->query($list_sql);
	  	
	  		//把计划金额累计到对账详情表中的订单中
			$order_sql = "UPDATE `db_account_order_list` SET `plan_amount` = `plan_amount` + '$plan_amount' WHERE `listid` = ".$data['order_listid'];

			$db->query($order_sql);
			//把计划金额加入到对账汇总表中
			$account_sql = "UPDATE `db_material_account` SET `tot_plan_amount` = `tot_plan_amount` + '$plan_amount' WHERE `accountid` = '$accountid'";
	
			$db->query($account_sql);
	  		header('location:'.$_SERVER['HTTP_REFERER']);
	  	

	  }
	}elseif($action == 'add_plan'){
		$plan_date = date('Y-m-d');
		//自动生成付款单编号
		$sql_number = "SELECT MAX((SUBSTRING(`plan_number`,-2)+0)) AS `max_number` FROM `db_material_funds_plan` WHERE DATE_FORMAT(`plan_date`,'%Y-%m-%d') = '$plan_date'";
		$result_number = $db->query($sql_number);
		if($result_number->num_rows){
			$array_number = $result_number->fetch_assoc();
			$max_number = $array_number['max_number'];
			$next_number = $max_number + 1;
			$plan_number = 'F'.date('Ymd',strtotime($plan_date)).strtolen($next_number,2).$next_number;
		}else{
			$plan_number = 'F'.date('Ymd',strtotime($plan_date))."01";
		} 
		$dotime = fun_gettime();
		$sql = "INSERT INTO `db_material_funds_plan` (`planid`,`plan_number`,`plan_date`,`employeeid`,`dodate`,`plan_status`) VALUES (NULL,'$plan_number','$plan_date','$employeeid','$dotime',0)";
		
		$db->query($sql);
		if($planid = $db->insert_id){
			header('location:funds_plan_list_add.php?id='.$planid);
		}
	}elseif($action == 'add_prepayment'){
		//接受数据
		$supplierid = intval($_POST['supplierid']);
		$order_date = $_POST['order_date'];
		$order_number = $_POST['order_number'];
		$prepayment = trim($_POST['prepayment']);
		$account_type = $_POST['account_type'];

		$date = date('Y-m-d');
		$sql = "INSERT INTO `db_funds_prepayment`(`order_date`,`order_number`,`prepayment`,`employeeid`,`dotime`,`supplierid`,`account_type`) VALUES('$order_date','$order_number','$prepayment','$employeeid','$date','$supplierid','$account_type')";
		$db->query($sql);
		if($db->affected_rows){
			header('location:material_funds_manage.php');
		}
	}elseif($action == 'show'){
		//接收数据
		$accountid_array = $_POST['accountid'];
		$plan_amount_array = $_POST['plan_amount'];
		$planid = $_POST['planid'];
		//添加计划列表
		$i = 0;
		foreach($plan_amount_array as $key=>$value){
			if(trim($value)){
				$sql = "INSERT INTO `db_funds_plan_list`(`planid`,`accountid`,`plan_amount`) VALUES('$planid','$accountid_array[$key]','$value')";
				$db->query($sql);
				if(!$db->affected_rows){
					$i++;
				}else{
					//更改对应的计划金额
					$account_sql = "UPDATE `db_material_account` SET `apply_amount` = `apply_amount` + '$value'  WHERE `accountid`= $accountid_array[$key]";
					$db->query($account_sql);
					$status_sql = "UPDATE `db_material_account` SET `status`='C' WHERE `amount` <= `apply_amount`";
					$db->query($status_sql);

				}
			}
		}
		if($i == 0){
			header('location:material_funds_plan.php');
		}
	}elseif($action == 'approval'){
		$method = $_GET['method'];
		//总经办审批
		$planid = $_GET['planid'];
		//更改付款计划的状态
		if($method == 'complete'){
			$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '5' WHERE `planid` = '$planid'";
		}elseif($method == 'back'){
			$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '1' WHERE `planid` = '$planid'"; 
		}
		$db->query($sql);
		if($db->affected_rows){
			header('location:material_funds_plan.php');
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
		$listid = $_GET['listid'];
		//获取计划金额
		$plan_amount_sql = "SELECT `db_funds_plan_list`.`plan_amount`,`db_funds_plan_list`.`order_listid`,`db_account_order_list`.`accountid` FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid` WHERE `db_funds_plan_list`.`listid` = '$listid'";
		$result_plan = $db->query($plan_amount_sql);
		if($result_plan->num_rows){
			$row_plan = $result_plan->fetch_assoc();
		}
		$plan_amount = $row_plan['plan_amount'];
		$order_listid = $row_plan['order_listid'];
		$accountid = $row_plan['accountid'];

		//更改对账详情中的计划金额
		$order_sql = "UPDATE `db_account_order_list` SET `plan_amount` = `plan_amount` -'$plan_amount' WHERE `listid` = '$order_listid'";
		$db->query($order_sql);
		$account_sql = "UPDATE `db_material_account` SET `tot_plan_amount` = `tot_plan_amount` - '$plan_amount' WHERE `accountid` = '$accountid'";
		$db->query($account_sql);
		//删除计划详情表中对应的项
		$plan_list_sql = "DELETE FROM `db_funds_plan_list` WHERE `listid` = '$listid'";
		$db->query($plan_list_sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
		var_dump($plan_amount);exit;
		//查找当前计划详情
		$list_sql = "SELECT * FROM `db_funds_plan_list` WHERE `listid` = '$plan_listid'";
		$result_list = $db->query($list_sql);
		if($result_list->num_rows){
			$row_list = $result_list->fetch_assoc();
		}
		$plan_amount = $row_list['plan_amount'];
		$listid = $row_list['listid'];
		$orderid = $row_list['orderid'];
		$accountid = $row_list['accountid'];
		
		//对账单中的项目，删除之后更改申请金额
		if(!empty($accountid)){
			$account_sql = "UPDATE `db_material_account` SET `apply_amount` = `apply_amount` - '$plan_amount',`status`='P' WHERE `accountid` = '$accountid'";
			
			$db->query($account_sql);
			
			$list_del_sql = "DELETE FROM `db_funds_plan_list` WHERE `listid` = '$listid'";
			$db->query($list_del_sql);
			if($db->affected_rows){
				header('location:'.$_SERVER['HTTP_REFERER']);
			}
			
		} else {
			//更改状态
			$prepayment_sql = "UPDATE `db_material_order` SET `prepayment` = `prepayment` - '$plan_amount' WHERE `orderid` = '$orderid'";

			$db->query($prepayment_sql);
			//直接删除预付款项
			$list_del_sql = "DELETE FROM `db_funds_plan_list` WHERE `listid` = '$plan_listid'";
			$db->query($list_del_sql);
			if($db->affected_rows){
				header('location:'.$_SERVER['HTTP_REFERER']);
			}
		}
	}
?>