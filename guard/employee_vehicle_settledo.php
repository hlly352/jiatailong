<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$passby = trim($_POST['passby']);
	$start_time = $_POST['start_time'];
	$finish_time = $_POST['finish_time'];
	$odometer_start = $_POST['odometer_start'];
	$odometer_finish = $_POST['odometer_finish'];
	$wait_time = $_POST['wait_time'];
	$confirmer_out = $_POST['confirmer_out'];
	$confirmer_in = $_POST['confirmer_in'];
	$reckoner = $_SESSION['employee_info']['employeeid'];
	$charge_parking = $_POST['charge_parking'];
	$charge_toll = $_POST['charge_toll'];
	$vehicle_status = $_POST['vehicle_status'];
	$settle_time = fun_gettime();
	$listid = $_POST['listid'];
	$sql = "UPDATE `db_vehicle_list` SET `passby` = '$passby',`start_time` = '$start_time',`finish_time` = '$finish_time',`odometer_start` = '$odometer_start',`odometer_finish` = '$odometer_finish',`wait_time` = '$wait_time',`confirmer_out` = '$confirmer_out',`confirmer_in` = '$confirmer_in',`reckoner` = '$reckoner',`charge_parking` = '$charge_parking',`charge_toll` = '$charge_toll',`vehicle_status` = '$vehicle_status',`settle_time` = '$settle_time' WHERE `listid` = '$listid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_POST['pre_url']);
	}
}
?>