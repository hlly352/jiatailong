<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
if($_SERVER['HTTP_REFERER']){
	$employeeid = $_POST['employeeid'];
	$sql = "SELECT * FROM `db_employee` WHERE `superior` = '$employeeid' AND `employee_status` = 1";
	$result = $db->query($sql);
	echo $result->num_rows;
}
?>