<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$employeeid = $_POST['applyer'];
$leavetime = $_POST['leavetime'];
//查询该员工的加班时间
$sql_overtime = "SELECT `overtime_valid` FROM `db_employee_overtime` WHERE `applyer` = '$employeeid' AND `approve_status` = 'B' AND `overtime_status` = 1 AND `confirmer` != 0";
$result_overtime = $db->query($sql_overtime);
if($result_overtime->num_rows){
	while($row_overtime = $result_overtime->fetch_assoc()){
		$over_validtime += $row_overtime['overtime_valid'];
	}
}else{
	$over_validtime = 0;
}
if($leavetime > $over_validtime){
	echo 0;
}else{
	echo 1;
}
?>