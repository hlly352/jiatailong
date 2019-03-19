<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$positionid = $_POST['positionid'];
$deptid = $_POST['deptid'];
$year = $_POST['year'];
$sql = "SELECT `month`,`quantity` FROM `db_personnel_staffing` WHERE `positionid` = '$positionid' AND `deptid` = '$deptid' AND DATE_FORMAT(`month`,'%Y') = '$year'";
$result = $db->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		$array .= $row['month'].'|'.$row['quantity'].'#';
	}
	echo rtrim($array,'#');
}
?>