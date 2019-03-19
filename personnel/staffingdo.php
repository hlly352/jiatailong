<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
if($_POST['submit']){
	$deptid = $_POST['deptid'];
	$positionid = $_POST['positionid'];
	$year = $_POST['year'];
	$array_quantity = $_POST['quantity'];
	foreach($array_quantity as $quantity_key=>$quantity_value){
		$month = date('Y-m-d',strtotime($year."-".($quantity_key+1)));
		$quantity = $quantity_value;
		$sql = "SELECT `staffingid` FROM `db_personnel_staffing` WHERE `deptid` = '$deptid' AND `positionid` = '$positionid' AND `month` = '$month'";
		$result = $db->query($sql);
		if($result->num_rows){
			$array = $result->fetch_assoc();
			$staffingid = $array['staffingid'];
			$sql_update = "UPDATE `db_personnel_staffing` SET `quantity` = '$quantity' WHERE `staffingid` = '$staffingid'";
			$db->query($sql_update);
		}else{
			$sql_add = "INSERT INTO `db_personnel_staffing` (`staffingid`,`positionid`,`deptid`,`month`,`quantity`) VALUES (NULL,'$positionid','$deptid','$month','$quantity')";
			$db->query($sql_add);
		}
	}
	header("location:".$_SERVER['HTTP_REFERER']);
}
?>