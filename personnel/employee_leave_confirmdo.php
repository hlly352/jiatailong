<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$leaveid = $_POST['leaveid'];
	$work_shift = $_POST['work_shift'];
	$start_time = $_POST['start_time'];
	$finish_time = $_POST['finish_time'];
	$leavetime = $_POST['leavetime'];
	$vacationid = $_POST['vacationid'];
	$leave_status = $_POST['leave_status'];
	$pre_url = $_POST['pre_url'];
	$confirmer = $_SESSION['employee_info']['employeeid'];
	$confirm_time = fun_gettime();
	$sql = "UPDATE `db_employee_leave` SET `work_shift` = '$work_shift',`start_time` = '$start_time',`finish_time` = '$finish_time',`leavetime` = '$leavetime',`leavetime_valid` = '$leavetime',`vacationid` = '$vacationid',`leave_status` = '$leave_status',`confirmer` = '$confirmer',`confirm_time` = '$confirm_time' WHERE `leaveid` = '$leaveid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$pre_url);
	}
}
?>