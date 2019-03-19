<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$gooutid = $_POST['gooutid'];
	$start_time = $_POST['start_time'];
	$finish_time = $_POST['finish_time'];
	$goout_status = $_POST['goout_status'];
	$sql_update = "UPDATE `db_employee_goout` SET `start_time` = '$start_time',`finish_time` = '$finish_time',`goout_status` = '$goout_status' WHERE `gooutid` = '$gooutid'";
	$db->query($sql_update);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>