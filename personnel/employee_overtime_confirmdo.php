<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$overtimeid = $_POST['overtimeid'];
	$start_time = $_POST['start_time'];
	$finish_time = $_POST['finish_time'];
	$overtime = $_POST['overtime'];
	$overtime_status = $_POST['overtime_status'];
	$pre_url = $_POST['pre_url'];
	$confirmer = $_SESSION['employee_info']['employeeid'];
	$confirm_time = fun_gettime();
	$sql = "UPDATE `db_employee_overtime` SET `start_time` = '$start_time',`finish_time` = '$finish_time',`overtime` = '$overtime',`overtime_valid` = '$overtime',`overtime_status` = '$overtime_status',`confirmer` = '$confirmer',`confirm_time` = '$confirm_time' WHERE `overtimeid` = '$overtimeid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$pre_url);
	}
}
?>