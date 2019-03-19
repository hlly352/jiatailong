<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$expressid = $_POST['expressid'];
	$applyer = $_SESSION['employee_info']['employeeid'];
	$apply_time = fun_gettime();
	$sql = "UPDATE `db_employee_express_receive` SET `applyer` = '$applyer',`apply_time` = '$apply_time',`apply_status` = 1 WHERE `expressid` = '$expressid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:employee_express_receive.php");
	}
}
?>