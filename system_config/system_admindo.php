<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_POST['employeeid'];
$systemid = $_POST['systemid'];
$sql = "SELECT `isadmin` FROM `db_system_employee` WHERE `employeeid` = '$employeeid' AND `systemid` = '$systemid'";
$result = $db->query($sql);
if($result->num_rows){
	$array = $result->fetch_assoc();
	$isadmin = $array['isadmin'];
	if($isadmin == 1){
		$sql_update = "UPDATE `db_system_employee` SET `isadmin` = 0 WHERE `employeeid` = '$employeeid' AND `systemid` = '$systemid' AND `isadmin` = 1";
		$db->query($sql_update);
		if($db->affected_rows){
			echo "del";
		}
	}elseif($isadmin == 0){
		$sql_update = "UPDATE `db_system_employee` SET `isadmin` = 1 WHERE `employeeid` = '$employeeid' AND `systemid` = '$systemid' AND `isadmin` = 0";
		$db->query($sql_update);
		if($db->affected_rows){
			echo "add";
		}
	}
}
?>