<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$overtimeid = $_POST['overtimeid'];
	$leaveid = $_POST['leaveid'];
	$deduction_time = $_POST['deduction_time'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	$sql = "INSERT INTO `db_leave_overtime` (`lvid`,`leaveid`,`overtimeid`,`deduction_time`,`employeeid`,`dotime`) VALUES (NULL,'$leaveid','$overtimeid','$deduction_time','$employeeid','$dotime')";
	$db->query($sql);
	  if($db->insert_id){
		  $sql_update_leave = "UPDATE `db_employee_leave` SET `leavetime_valid` = `leavetime_valid` - '$deduction_time' WHERE `leaveid` = '$leaveid'";
		  $db->query($sql_update_leave);
		  $sql_update_over = "UPDATE `db_employee_overtime` SET `overtime_valid` = `overtime_valid` - '$deduction_time' WHERE `overtimeid` = '$overtimeid'";
		  $db->query($sql_update_over);
		  header("location:".$_POST['pre_url']);
	  }
}
?>