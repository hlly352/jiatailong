<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$deduction_time = $_POST['deduction_time'];
$leaveid = $_POST['leaveid'];
$overtimeid = $_POST['overtimeid'];
$sql_overtime = "SELECT `overtime_valid` FROM `db_employee_overtime` WHERE `overtimeid` = '$overtimeid'";
$result_overtime = $db->query($sql_overtime);
$array_overtime = $result_overtime->fetch_assoc();
$overtime_valid = $array_overtime['overtime_valid'];
$sql_leave = "SELECT `leavetime_valid` FROM `db_employee_leave` WHERE `leaveid` = '$leaveid'";
$result_leave = $db->query($sql_leave);
$array_leave = $result_leave->fetch_assoc();
$leavetime_valid = $array_leave['leavetime_valid'];
if($deduction_time > $overtime_valid || $deduction_time > $leavetime_valid){
	echo 1;
}
?>