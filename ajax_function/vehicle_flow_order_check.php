<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$action = $_POST['action'];
$flow_order = $_POST['flow_order'];
$deptid = $_POST['deptid'];
if($action == "add"){
	$sql = "SELECT * FROM `db_vehicle_flow` WHERE `deptid` = '$deptid' AND `flow_order` = '$flow_order'";
}elseif($action == "edit"){
	$flowid = $_POST['flowid'];
	$sql = "SELECT * FROM `db_vehicle_flow` WHERE `deptid` = '$deptid' AND `flow_order` = '$flow_order' AND `flowid` != '$flowid'";
}
$result = $db->query($sql);
if($result->num_rows){
	echo "序号重复，请重新输入！";
}
?>