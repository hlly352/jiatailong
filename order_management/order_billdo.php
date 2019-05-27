<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];

$action = $_GET['action'];
//执行添加操作
if($action == 'add'){
	$data = $_POST;
	//判断是添加还是修改
	
	if($data['bill_id']){
		$bill_id = $data['bill_id'];

		unset($data['bill_id']);
		//拼接更新sql 语句
		$bill_str = ' ';
		foreach($data as $k=>$v){
			$bill_str .='`'.$k.'`="'.$v.'",'; 
		}
		$bill_str .='`add_time`="'.time().'"';
		$bill_sql = "UPDATE `db_order_bill` SET ".$bill_str." WHERE `bill_id` =".$bill_id;

	} else {
	//拼接插入的sql 语句
	$sql_key = ' ';
	$sql_value = ' ';
	foreach($data as $key=>$value){
		$sql_key .= '`'.$key.'`,';
		$sql_value .= '"'.$value.'",';
	}
	 $sql_key .= '`employeeid`,`add_time`';
	 $sql_value .= '"'.$employeeid.'",'.time();
	$bill_sql = "INSERT INTO `db_order_bill`($sql_key) VALUES($sql_value)";
		}
	$result = $db->query($bill_sql);
	if($db->affected_rows){
		header('location:order_bill.php');
		}
	
}


?>