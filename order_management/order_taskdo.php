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
	//去除客户代码
	$customer_code = $data['customer_code'];
	unset($data['customer_code']);
	//拼接插入的sql 语句
	$sql_key = ' ';
	$sql_value = ' ';
	foreach($data as $key=>$value){
		$sql_key .= '`'.$key.'`,';
		$sql_value .= '"'.$value.'",';
	}
	 $sql_key .= '`employeeid`,`deal_time`,`is_approval`,`is_deal`';
	 $sql_value .= '"'.$employeeid.'","'.time().'","1","1"';
	$task_sql = "INSERT INTO `db_mould_data`($sql_key) VALUES($sql_value)";

	$result = $db->query($task_sql);
	echo $db->affected_rows;
	if($db->affected_rows){
		//插入客户代码
		$customer_sql = "UPDATE `db_customer_info` SET `customer_code`=\"{$customer_code}\" WHERE `customer_id`=".$data['client_name'];

		header('location:order_gather.php');
	
		
	}

}


?>