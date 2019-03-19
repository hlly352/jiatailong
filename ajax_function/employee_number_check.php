<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$action = $_POST['action'];
$employee_number = $_POST['employee_number'];
if($action == "add"){
	$sql = "SELECT * FROM `db_employee` WHERE `employee_number` = '$employee_number'";
	$result = $db->query($sql);
	if($result->num_rows){
		echo $employee_number."已在存在，请确认后再添加";
	}
}elseif($action == "edit"){
	$employeeid = $_POST['employeeid'];
	$sql = "SELECT * FROM `db_employee` WHERE `employee_number` = '$employee_number' AND `employeeid` != '$employeeid'";
	$result = $db->query($sql);
	if($result->num_rows){
		echo $employee_number."已在存在，请确认后再修改";
	}
}
?>