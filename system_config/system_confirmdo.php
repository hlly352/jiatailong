<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_POST['employeeid'];
$systemid = $_POST['systemid'];
$sql = "SELECT `isconfirm` FROM `db_system_employee` WHERE `employeeid` = '$employeeid' AND `systemid` = '$systemid'";
$result = $db->query($sql);
if($result->num_rows){
	$array = $result->fetch_assoc();
	$isconfirm = $array['isconfirm'];
	if($isconfirm == 1){
		$sql_update = "UPDATE `db_system_employee` SET `isconfirm` = 0 WHERE `employeeid` = '$employeeid' AND `systemid` = '$systemid' AND `isconfirm` = 1";
		$db->query($sql_update);
		if($db->affected_rows){
			echo "del";
		}
	}elseif($isconfirm == 0){
		$sql_update = "UPDATE `db_system_employee` SET `isconfirm` = 1 WHERE `employeeid` = '$employeeid' AND `systemid` = '$systemid' AND `isconfirm` = 0";
		$db->query($sql_update);
		if($db->affected_rows){
			echo "add";
		}
	}	
}
?>