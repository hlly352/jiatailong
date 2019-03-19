<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_POST['employeeid'];
$systemid = $_POST['systemid'];
$sql = "SELECT * FROM `db_system_employee` WHERE `employeeid` = '$employeeid' AND `systemid` = '$systemid'";
$result = $db->query($sql);
if($result->num_rows){
	$sql_del = "DELETE FROM `db_system_employee` WHERE `employeeid` = '$employeeid' AND `systemid` = '$systemid'";
	$db->query($sql_del);
	if($db->affected_rows){
		echo "del";
	}
}else{
	$sql_add = "INSERT INTO `db_system_employee` (`id`,`systemid`,`employeeid`) VALUES (NULL,'$systemid','$employeeid')";
	$db->query($sql_add);
	if($db->insert_id){
		echo "add";
	}
}
?>