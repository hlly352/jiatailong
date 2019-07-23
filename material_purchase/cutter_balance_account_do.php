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
$nowmonth = date('Y-m');
//通过inoutid查询供应商
foreach($id as $v){
	$supplier_sql = "SELECT `db_cutter_order`.`supplierid` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_inout`.`listid` = `db_cutter_order_list`.`listid` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` WHERE `db_cutter_inout`.`inoutid` = ".$v;
	$result_supplier = $db->query($supplier_sql);
	if($result_supplier->num_rows){
		while($row = $result_supplier->fetch_assoc()){
			//通过供应商查找对账汇总表里是否存在
			$account_sql = "SELECT `accountid` FROM `db_cutter_account` WHERE `supplierid` = ".$row['supplierid']." AND `account_time` LIKE '$nowmonth%'";
			$result_account = $db->query($account_sql);
			if($result_account->num_rows){
				$accountid = $result_account->fetch_row()[0];
			}else{
				//生成对账号
				$number_sql = "SELECT MAX((SUBSTRING(`account_number`,-2)+0)) AS `number` FROM `db_cutter_account` WHERE `account_time` = '".date('Y-m-d')."'";
				$result_number = $db->query($number_sql);
				if($result_number->num_rows){
					$array_number = $result_number->fetch_assoc();
					$max_number = $array_number['number'];
					$max_number = $max_number + 1;
					$account_number = 'C'.date('Ymd').strtolen($max_number,2).$max_number;
				} else {
					$account_number = 'C'.date('Ymd')."01";
				}
				//没有则新建一条汇总
				$time = date('Y-m-d');
				$add_sql = "INSERT INTO `db_cutter_account`(`account_time`,`supplierid`,`employeeid`,`account_number`) VALUES('$time',".$row['supplierid'].",'$employeeid','$account_number')";
				$db->query($add_sql);
				if($db->affected_rows){
					$accountid = $db->insert_id;
				}
			}
		}
	}
	//把当前记录插入到对账详情表中
	$list_sql = "INSERT INTO `db_cutter_account_list`(`accountid`,`inoutid`) VALUES('$accountid',$v)";
	$db->query($list_sql);
	if($db->affected_rows){
		//把金额加入到汇总表中
		$summary_sql = "UPDATE `db_cutter_account` SET `amount` = `amount`+(SELECT `amount` FROM `db_material_inout` WHERE inoutid = '$v') WHERE `accountid`=".$accountid;
		$db->query($summary_sql);
	}
}
//更改入库记录中的对账状态
$sql = "UPDATE `db_material_inout` SET `account_status` = 'F' WHERE `inoutid` IN($inoutid)";
$db->query($sql);
if($db->affected_rows){
	header("location:".$_SERVER['HTTP_REFERER']);
}


?>