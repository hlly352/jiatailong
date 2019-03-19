<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$action = $_POST['action'];
$employee_name = $_POST['employee_name'];
if($action == "add"){
	$sql = "SELECT * FROM `db_employee` WHERE `employee_name` = '$employee_name'";
	$result = $db->query($sql);
	if($result->num_rows){
		echo $employee_name."已在存在，请确认后再添加";
	}
}elseif($action == "edit"){
	$employeeid = $_POST['employeeid'];
	$sql = "SELECT * FROM `db_employee` WHERE `employee_name` = '$employee_name' AND `employeeid` != '$employeeid'";
	$result = $db->query($sql);
	if($result->num_rows){
		echo $employee_name."已在存在，请确认后再修改";
	}
}
?>