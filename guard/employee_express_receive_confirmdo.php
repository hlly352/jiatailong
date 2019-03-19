<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$expressid = $_POST['expressid'];
	$confirmor = $_SESSION['employee_info']['employeeid'];
	$confirm_time = fun_gettime();
	$sql = "UPDATE `db_employee_express_receive` SET `confirmor` = '$confirmor',`confirm_time` = '$confirm_time',`get_status` = 1 WHERE `expressid` = '$expressid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_POST['pre_url']);
	}
}
?>