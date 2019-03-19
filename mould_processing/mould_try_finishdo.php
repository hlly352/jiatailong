<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$tryid = $_POST['tryid'];
	$supplierid = $_POST['supplierid'];
	$order_number = trim($_POST['order_number']);
	$try_date = $_POST['try_date'];
	$unit_price = $_POST['unit_price'];
	$cost = $_POST['cost'];
	$finish_confirmor = $_SESSION['employee_info']['employeeid'];
	$finish_confirm_time = fun_gettime();
	$sql = "UPDATE `db_mould_try` SET `supplierid` = '$supplierid',`order_number` = '$order_number',`try_date` = '$try_date',`unit_price` = '$unit_price',`cost` = '$cost',`finish_status` = 1,`finish_confirmor` = '$finish_confirmor',`finish_confirm_time` = '$finish_confirm_time' WHERE `tryid` = '$tryid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_POST['pre_url']);
	}
}
?>