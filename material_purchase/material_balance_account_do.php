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
//通过inoutid查询供应商
foreach($id as $v){
	$supplier_sql = "SELECT `db_material_order`.`supplierid`,`db_material_order`.`orderid` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_inout`.`listid` = `db_material_order_list`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` WHERE `db_material_inout`.`inoutid` = ".$v;
	$result_supplier = $db->query($supplier_sql);
	if($result_supplier->num_rows){
		while($row = $result_supplier->fetch_assoc()){
			//获取订单id
			$orderid_array[] = $row['orderid'];
	
			//通过供应商查找对账汇总表里是否存在
			$account_sql = "SELECT `accountid` FROM `db_material_account` WHERE `supplierid` = ".$row['supplierid']." AND `account_time` LIKE '$nowmonth%' AND `account_type` = 'M'";
			$result_account = $db->query($account_sql);
			if($result_account->num_rows){
				$accountid = $result_account->fetch_row()[0];
			}else{
				//生成对账号
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
				$add_sql = "INSERT INTO `db_material_account`(`account_time`,`supplierid`,`employeeid`,`account_number`,`account_type`) VALUES('$time',".$row['supplierid'].",'$employeeid','$account_number','M')";
				
				$db->query($add_sql);
				if($db->affected_rows){
					$accountid = $db->insert_id;
				}
			}
		}
	}
	//把当前记录插入到对账详情表中
	$list_sql = "INSERT INTO `db_material_account_list`(`accountid`,`inoutid`) VALUES('$accountid',$v)";
	$db->query($list_sql);
	if($db->affected_rows){
		//把金额加入到汇总表中
		$summary_sql = "UPDATE `db_material_account` SET `amount` = `amount`+(SELECT (`amount` + `process_cost` - `cancel_amount` - `cut_payment`) FROM `db_material_inout` WHERE inoutid = '$v') WHERE `accountid`=".$accountid;
		$db->query($summary_sql);
	}
}
//查询原来的orderidlist值
$orderidlist_sql = "SELECT `orderidlist` FROM `db_material_account` WHERE `accountid` = '$accountid'";
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

$insert_orderid_sql = "UPDATE `db_material_account` SET `orderidlist` = '$orderlist' WHERE `accountid` = '$accountid'";
$db->query($insert_orderid_sql);
//更改入库记录中的对账状态
$sql = "UPDATE `db_material_inout` SET `account_status` = 'F' WHERE `inoutid` IN($inoutid)";
$db->query($sql);
if($db->affected_rows){
	header("location:".$_SERVER['HTTP_REFERER']);
}


?>