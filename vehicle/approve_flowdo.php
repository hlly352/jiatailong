<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$flow_order = $_POST['flow_order'];
	$approver = $_POST['approver'];
	$certigier = $_POST['certigier'];
	$iscontrol = $_POST['iscontrol'];
	$deptid = $_POST['deptid'];
	if($action == "add"){
		$sql = "INSERT INTO `db_vehicle_flow` (`flowid`,`approver`,`certigier`,`iscontrol`,`flow_order`,`deptid`) VALUES (NULL,'$approver','$certigier','$iscontrol','$flow_order','$deptid')";
		$db->query($sql);
		if($db->insert_id){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "edit"){
		$flowid = $_POST['flowid'];
		$sql = "UPDATE `db_vehicle_flow` SET `flow_order` = '$flow_order',`approver` = '$approver',`certigier` = '$certigier',`iscontrol` = '$iscontrol' WHERE `flowid` = '$flowid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$flowid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_vehicle_flow` WHERE `flowid` IN ($flowid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>