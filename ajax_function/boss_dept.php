<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$boss_val = trim($_POST['boss_val']);
$action = $_POST['action'];
//查询部门
$sql = "SELECT b.`dept_name`,a.`employee_name` FROM `db_employee` as a  INNER JOIN `db_department` as b ON a.`deptid`=b.`deptid`  WHERE a.`employee_name` LIKE '%".$boss_val."%'";

$result = $db->query($sql);
if($result->num_rows){
	$dept = $result->fetch_row();
	$dept = $dept[0].'##'.$dept[1];
	echo $dept;
}
?>