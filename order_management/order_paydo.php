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
	//拼接插入的sql 语句
	$sql_key = ' ';
	$sql_value = ' ';
	foreach($data as $key=>$value){
		$sql_key .= '`'.$key.'`,';
		$sql_value .= '"'.$value.'",';
	}
	 $sql_key .= '`employeeid`,`add_time`';
	 $sql_value .= '"'.$employeeid.'",'.time();
	$pay_sql = "INSERT INTO `db_order_pay`($sql_key) VALUES($sql_value)";

	$result = $db->query($pay_sql);
	if($db->affected_rows){
		header('location:order_pay.php');
	}

}


?>