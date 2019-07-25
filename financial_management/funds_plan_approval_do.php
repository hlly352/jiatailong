<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$planid = $_GET['planid'];
$action = $_GET['action'];
//遍历accountid，更改对账状态
if($action == 'complete'){
	$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '3' WHERE `planid`= '$planid'";
	 $db->query($sql);
    if($db->affected_rows){
    	header('location:funds_plan_approval.php');
    	}
} elseif($action == 'back'){
		$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '0' WHERE `planid` = '$planid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:funds_plan_approval.php');
		}
	}
?>