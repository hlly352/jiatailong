<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	if($action == "edit"){
		$listid = $_POST['listid'];
		$apply_date = $_POST['apply_date'];
		$dotype = $_POST['dotype'];
		$vehicleid = $_POST['vehicleid'];
		$pathtype = $_POST['pathtype'];
		$charge = $_POST['charge'];
		$charge_wait = $_POST['charge_wait'];
		$departure = trim($_POST['departure']);
		$destination = trim($_POST['destination']);
		$passby = trim($_POST['passby']);
		$roundtype = $_POST['roundtype'];
		$start_time = $_POST['start_time'];
		$finish_time = $_POST['finish_time'];
		$odometer_start = $_POST['odometer_start'];
		$odometer_finish = $_POST['odometer_finish'];
		$charge_toll = $_POST['charge_toll'];
		$charge_parking = $_POST['charge_parking'];
		$wait_time = $_POST['wait_time'];
		$other = trim($_POST['other']);
		$cause = trim($_POST['cause']);
		$vehicle_status = $_POST['vehicle_status'];
		$sql = "UPDATE `db_vehicle_list` SET `apply_date` = '$apply_date',`dotype` = '$dotype',`vehicleid` = '$vehicleid',`pathtype` = '$pathtype',`charge` = '$charge',`charge_wait` = '$charge_wait',`departure` = '$departure',`destination` = '$destination',`passby` = '$passby',`roundtype` = '$roundtype',`start_time` = '$start_time',`finish_time` = '$finish_time',`odometer_start` = '$odometer_start',`odometer_finish` = '$odometer_finish',`charge_toll` = '$charge_toll',`charge_parking` = '$charge_parking',`wait_time` = '$wait_time',`other` = '$other',`cause` = '$cause',`vehicle_status` = '$vehicle_status' WHERE `listid` = '$listid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$listid = fun_convert_checkbox($array_id);
		$sql_approve = "DELETE FROM `db_office_approve` WHERE `linkid` IN ($listid) AND `approve_type` = 'V'";
		$db->query($sql_approve);
		$sql = "DELETE FROM `db_vehicle_list` WHERE `listid` IN ($listid)";
		$db->query($sql);
		if($db->affected_rows){
		  header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
	
}
?>