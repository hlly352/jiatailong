<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$deptid = trim($_POST['deptid']);
//查询部门的人员
$sql = "SELECT `employeeid`,`employee_name` FROM `db_employee` WHERE `deptid` = '$deptid'";

$result = $db->query($sql);
if($result->num_rows){
	$employee = array();
	while($row = $result->fetch_assoc()){
		$employee[] = $row;
	}
}
echo json_encode($employee);
?>