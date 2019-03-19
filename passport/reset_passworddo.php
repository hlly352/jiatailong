<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
if($_POST['submit']){
	$account = trim($_POST['account']);
	$employee_name = trim($_POST['employee_name']);
	$password = $_POST['password'];
	$sql = "SELECT `employeeid` FROM `db_employee` WHERE `account` = '$account' AND `employee_name` = '$employee_name'";
	$result = $db->query($sql);
	if($result->num_rows){
		$array = $result->fetch_assoc();
		$employeeid = $array['employeeid'];
		$password = md5($newpassword.ALL_PW);
		$sql_update = "UPDATE `db_employee` SET `password` = '$password' WHERE `employeeid` = '$employeeid'";
		$db->query($sql);
		header("location:login.php");
	}else{
		header("location:".$_SERVER['HTTP_REFERER']."&do_status=F");
	}
}
?>