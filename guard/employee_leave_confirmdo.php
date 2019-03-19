<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$leaveid = $_POST['leaveid'];
	$dotype = $_POST['dotype'];
	if($dotype == 'O'){
		$confirmer_out = $_SESSION['employee_info']['employeeid'];
		$confirmtime_out = fun_gettime();
		$sql = "UPDATE `db_employee_leave` SET `confirmer_out` = '$confirmer_out',`confirmtime_out` = '$confirmtime_out' WHERE `leaveid` = '$leaveid'";
	}elseif($dotype == 'I'){
		$confirmer_in = $_SESSION['employee_info']['employeeid'];
		$confirmtime_in = fun_gettime();
		$sql = "UPDATE `db_employee_leave` SET `confirmer_in` = '$confirmer_in',`confirmtime_in` = '$confirmtime_in' WHERE `leaveid` = '$leaveid'";
	}
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_POST['pre_url']);
	}
}
?>