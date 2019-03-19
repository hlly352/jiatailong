<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$plate_number = trim($_POST['plate_number']);
	$vehicle_type = $_POST['vehicle_type'];
	$owner = trim($_POST['owner']);
	$contact = trim($_POST['contact']);
	$charge_out = $_POST['charge_out'];
	$charge_in = $_POST['charge_in'];
	$charge_wait = $_POST['charge_wait'];
	if($action == "add"){
		$sql = "INSERT INTO `db_vehicle` (`vehicleid`,`plate_number`,`vehicle_type`,`owner`,`contact`,`charge_out`,`charge_in`,`charge_wait`,`vehicle_status`) VALUES (NULL,'$plate_number','$vehicle_type','$owner','$contact','$charge_out','$charge_in','$charge_wait',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:vehicle.php");
		}
	}elseif($action == "edit"){
		$vehicleid = $_POST['vehicleid'];
		$vehicle_status = $_POST['vehicle_status'];
		$sql = "UPDATE `db_vehicle` SET `plate_number` = '$plate_number',`vehicle_type` = '$vehicle_type',`owner` = '$owner',`contact` = '$contact',`charge_out` = '$charge_out',`charge_in` = '$charge_in',`charge_wait` = '$charge_wait',`vehicle_status` = '$vehicle_status' WHERE `vehicleid` = '$vehicleid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$id = $_POST['id'];
		$vehicleid = fun_convert_checkbox($id);
		$sql = "DELETE FROM `db_vehicle` WHERE `vehicleid` IN ($vehicleid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>