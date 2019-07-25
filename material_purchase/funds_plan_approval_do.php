<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
	$action = $_REQUEST['action'];
	$planid = $_GET['planid'];

	if($action == 'approval'){
		$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '6' WHERE `planid` = '$planid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:material_funds_plan.php');
		}
	}elseif($action == 'approval_edit'){
		$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '8' WHERE `planid` = '$planid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:material_funds_plan.php');
		}
	}
	?>