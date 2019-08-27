<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
//获取对账成功的记录id
if($_POST['submit']){
	$id = $_POST['id'];
	$inoutid = fun_convert_checkbox($id);
} else{
	$inoutid = $_GET['id'];
	$id[] = $_GET['id'];
}
$orderid_array = array();
$nowmonth = date('Y-m');
//查询有供应商的id
 	$supplier_sql = "SELECT `db_material_order`.`supplierid` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_inout`.`listid` = `db_material_order_list`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` WHERE `db_material_inout`.`inoutid` IN($inoutid)";
 	$result_supplier = $db->query($supplier_sql);
 	if($result_supplier->num_rows){
 		$supplier_list = array();
 		while($row_supplier = $result_supplier->fetch_assoc()){
 			$supplier_list[$row_supplier['supplierid']] = $row_supplier['supplierid'];
 		}
 	}
 //每个供应商创建一个对账汇总信息
 foreach($supplier_list as $v){
 		$number_sql = "SELECT MAX((SUBSTRING(`account_number`,-2)+0)) AS `number` FROM `db_material_account` WHERE `account_time` = '".date('Y-m-d')."'";
		$result_number = $db->query($number_sql);
		if($result_number->num_rows){
			$array_number = $result_number->fetch_assoc();
			$max_number = $array_number['number'];
			$max_number = $max_number + 1;
			$account_number = 'A'.date('Ymd').strtolen($max_number,2).$max_number;
			} else {
				$account_number = 'A'.date('Ymd')."01";
			}

			//没有则新建一条汇总
			$time = date('Y-m-d');
			$add_sql = "INSERT INTO `db_material_account`(`account_time`,`supplierid`,`employeeid`,`account_number`,`account_type`) VALUES('$time','$v','$employeeid','$account_number','M')";
			$db->query($add_sql);
			if($db->affected_rows){
				$accountid = $db->insert_id;
			}
			//查询当前供应商对应的对账信息
				$inout_sql = "SELECT `db_material_inout`.`inoutid`,`db_material_order`.`orderid` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_inout`.`listid` = `db_material_order_list`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` WHERE `db_material_order`.`supplierid` = '$v' AND `db_material_inout`.`inoutid` IN($inoutid)";
				
			$result_inout = $db->query($inout_sql);
			if($result_inout->num_rows){
				$inout_list = array();
				while($row_inout = $result_inout->fetch_assoc()){
					//加入到对账详情表中
					$list_sql = "INSERT INTO `db_material_account_list`(`accountid`,`inoutid`,`orderid`) VALUES('$accountid','".$row_inout['inoutid']."','".$row_inout['orderid']."')";
					
					$db->query($list_sql);
				}
			}
			$account_array[$accountid] = $accountid;
 }


//通过对账汇总表id 查询明细
foreach($account_array as $value){
	$account_list_sql = "SELECT `db_material_account_list`.`accountid`,`db_material_order`.`orderid`,`db_material_inout`.`process_cost`,`db_material_inout`.`cancel_amount`,`db_material_inout`.`cut_payment`,`db_material_inout`.`amount` FROM `db_material_account_list` INNER JOIN `db_material_inout` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` INNER JOIN `db_material_order_list` ON `db_material_inout`.`listid` = `db_material_order_list`.`listid` INNER JOIN `db_material_order` ON `db_material_order_list`.`orderid` = `db_material_order`.`orderid` WHERE `db_material_account_list`.`accountid` = '$value'";

	$result_account_list = $db->query($account_list_sql);
	$orderid_array = array();
	$tot_amount = $tot_cancel_amount = $tot_cut_payment = $tot_process_cost = 0;
	if($result_account_list){
		$orderid_array = array();
		while($row_account_list = $result_account_list->fetch_assoc()){
			//获取订单id
			$orderid_array[] = $row_account_list['orderid'];
			//获取总金额，加工费，核销，品质扣款
			$tot_amount += $row_account_list['amount'];
			$tot_cancel_amount += $row_account_list['cancel_amount'];
			$tot_cut_payment += $row_account_list['cut_payment'];
			$tot_process_cost += $row_account_list['process_cost'];
		}
	}
	//查询原来的orderidlist值
	$orderidlist_sql = "SELECT `orderidlist` FROM `db_material_account` WHERE `accountid` = '$value'";
	$result_orderidlist = $db->query($orderidlist_sql);
	if($result_orderidlist->num_rows){
		$str_orderidlist = $result_orderidlist->fetch_row()[0];
	}
	//把原来的orderid 变为数组

	if(!empty($str_orderidlist)){
		$old_orderidlist = array();
		if(count(explode(',',$str_orderidlist)) >1){
			$old_orderidlist = explode(',',$str_orderidlist);
		}else{
			$old_orderidlist[0] = $str_orderidlist;
		}
	}

	$new_orderid_array = array();
	if(!empty($old_orderidlist)){
		$new_orderid_array = array_merge($old_orderidlist,$orderid_array);
	}else{
		$new_orderid_array = $orderid_array;
	}
	//把合并后的订单号转化为字符串
	$orderid = array_unique($new_orderid_array);
	$orderidlist = fun_convert_checkbox($orderid);
	$orderlist = trim($orderidlist,',');

	//查询是否有预付款
	// $prepayment_sql = "SELECT `prepayment` FROM `db_funds_prepayment` WHERE `order_number` IN(SELECT `order_number` FROM `db_material_order` WHERE `orderid` IN($orderlist)) AND `status` = '1' AND `account_type` = 'M'";
	// $result_prepayment = $db->query($prepayment_sql);
	// if($result_prepayment->num_rows){
	// 	while($row_prepayment = $result_prepayment->fetch_assoc()){
	// 		$tot_prepayment += $row_prepayment['prepayment'];
	// 	}
	// }
	//把对账详情表中的汇总信息填入汇总表中
	$insert_orderid_sql = "UPDATE `db_material_account` SET `orderidlist` = '$orderlist',`tot_amount` = '$tot_amount',`tot_cancel_amount` = '$tot_cancel_amount',`tot_process_cost` = '$tot_process_cost',`tot_cut_payment` = '$tot_cut_payment' WHERE `accountid` = '$value'";

	$db->query($insert_orderid_sql);
	//查询对账信息，加入到账款管理表中
	$account_info_sql = "SELECT `account_time`,`accountid`,`supplierid`,(`tot_amount` + `tot_process_cost` - `tot_cancel_amount` - `tot_cut_payment`) AS `amount` FROM `db_material_account` WHERE `accountid` = '$value'";

	$result_account_info = $db->query($account_info_sql);
	if($result_account_info->num_rows){	
		$row_info = $result_account_info->fetch_assoc();
		}
	
		$funds_sql = "INSERT INTO `db_material_funds_list`(`accountid`,`supplierid`,`amount`,`approval_date`) VALUES('".$row_info['accountid']."','".$row_info['supplierid']."','".$row_info['amount']."','".$row_info['account_time']."')";
		$db->query($funds_sql);
	     //通过对账单号在对账详情表中查找订单信息
       $order_sql = "SELECT `db_material_order`.`orderid`,`db_material_order`.`order_number`,SUM(`db_material_inout`.`amount`) AS `sum`,SUM(`db_material_inout`.`cancel_amount`) AS `cancel_amount`,SUM(`db_material_inout`.`cut_payment`) AS `cut_payment`,SUM(`db_material_order_list`.`process_cost`) AS `process_cost` FROM `db_material_order` INNER JOIN `db_material_order_list` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_material_inout` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_supplier` ON `db_material_order`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_account_list` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` WHERE `db_material_account_list`.`accountid` = '$value' GROUP BY `db_material_order`.`orderid`";
      $result_order = $db->query($order_sql);
      $sqllist = '';
      if($result_order->num_rows){
      	while($row_order = $result_order->fetch_assoc()){
      		$orderid = $row_order['orderid'];
      		$order_number = $row_order['order_number'];
      		$order_amount = $row_order['sum'];
      		$cancel_amount = $row_order['cancel_amount'];
      		$cut_payment = $row_order['cut_payment'];
      		$process_cost = $row_order['process_cost'];
      		$sqllist .= "('$value','$orderid','$order_number','$order_amount','$process_cost','$cancel_amount','$cut_payment'),";
      	}
      }
	$sqllist = rtrim($sqllist,',');

	//插入对账订单信息
    $order_list_sql = "INSERT INTO `db_account_order_list`(`accountid`,`orderid`,`order_number`,`order_amount`,`process_cost`,`cancel_amount`,`cut_payment`) VALUES $sqllist";
   $db->query($order_list_sql);
}
    
	header("location:".$_SERVER['HTTP_REFERER']);
		

 ?>