<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$vehicleid = $_POST['vehicleid'];
$pathtype = $_POST['pathtype'];
$sql_vehicle = "SELECT `charge_out`,`charge_in`,`charge_wait` FROM `db_vehicle` WHERE `vehicleid` = '$vehicleid'";
$result_vehicle = $db->query($sql_vehicle);
if($result_vehicle->num_rows){
	$arr_vehile = $result_vehicle->fetch_assoc();
	$charge_out = $arr_vehile['charge_out'];
	$charge_in = $arr_vehile['charge_in'];
	$charge_wait = $arr_vehile['charge_wait'];
	if($pathtype == 'A'){
		$charge = $charge_in;
	}elseif($pathtype == 'B'){
		$charge = $charge_out;
	}
	echo $charge."#".$charge_wait;
}else{
	echo '0#0';
}
?>