<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once 'd:\site\global_mysql_connect.php';
require_once 'd:\site\function/function.php';
$sql_plan = "SELECT `planid`,DATEDIFF(`finish_date`,CURDATE()) AS `diff_date` FROM `db_job_plan` WHERE DATEDIFF(`finish_date`,CURDATE()) < 0 AND `plan_result` = 0 AND `plan_status` = 1 AND `planid` NOT IN (SELECT `planid` FROM `db_job_plan_list` GROUP BY `planid`)";
$result_plan = $db->query($sql_plan);
if($result_plan->num_rows){
	$dotime = fun_gettime();
	while($row_plan = $result_plan->fetch_assoc()){
		$planid = $row_plan['planid'];
		$sqladd .= "(NULL,'$planid','$dotime'),";
	}
	$sqladd = rtrim($sqladd,',');
	$sql = "INSERT INTO `db_job_plan_list` (`listid`,`planid`,`dotime`) VALUES $sqladd";
	$db->query($sql);
}
?>