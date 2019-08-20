<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = $_REQUEST['action'];
$planid = $_GET['planid'];

//更改计划状态
if($action == 'complete'){
	$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '3' WHERE `planid` = '$planid'";
	$db->query($sql);
	header('location:material_funds_plan.php');
}elseif($action == 'back'){
	$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '0' WHERE `planid` = '$planid'";
	$db->query($sql);
	header('location:material_funds_plan.php');
}
?>