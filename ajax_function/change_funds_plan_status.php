<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$planid = trim($_POST['planid']);
$employeeid = $_POST['employeeid'];
$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = IF(`plan_status`= 0,1,0) WHERE `planid` = '$planid' AND (`plan_status` = 0 OR `plan_status` = 1)";

$db->query($sql);
if($db->affected_rows){
	echo $planid;
}
?>