<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$gooutid = $_POST['gooutid'];
	$confirmer = $_SESSION['employee_info']['employeeid'];
	$dotype = $_POST['dotype'];
	$dotime = fun_gettime();
	if($dotype == 'O'){
		$start_time = $_POST['start_time'];
		$sql_update = "UPDATE `db_employee_goout` SET `start_time` = '$start_time',`confirmer_out` = '$confirmer',`confirmtime_out` = '$dotime' WHERE `gooutid` = '$gooutid'";
	}elseif($dotype == 'I'){
		$finish_time = $_POST['finish_time'];
		$sql_update = "UPDATE `db_employee_goout` SET `finish_time` = '$finish_time',`confirmer_in` = '$confirmer',`confirmtime_in` = '$dotime' WHERE `gooutid` = '$gooutid'";
	}
	$db->query($sql_update);
	if($db->affected_rows){
		header("location:".$_POST['pre_url']);
	}
}
?>