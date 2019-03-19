<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
//自动生成编号
$sql_number = "SELECT MAX((SUBSTRING(`apply_number`,-4)+0)) AS `max_number` FROM `db_cutter_apply` WHERE DATE_FORMAT(`apply_date`,'%Y') = YEAR(NOW())";
$result_number = $db->query($sql_number);
if($result_number->num_rows){
	$array_number = $result_number->fetch_assoc();
	$max_number = $array_number['max_number'];
	$next_number = $max_number+1;
	$apply_number = 'C'.date('Y').strtolen($next_number,4).$next_number;
}else{
	$apply_number = 'C'.date('Y')."0001";
}
$apply_date = fun_getdate();
$apply_time = fun_gettime();
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql = "INSERT INTO `db_cutter_apply` (`applyid`,`apply_number`,`apply_date`,`apply_time`,`employeeid`) VALUES (NULL,'$apply_number','$apply_date','$apply_time','$employeeid')";
$db->query($sql);
if($applyid = $db->insert_id){
	header("location:cutter_apply_list_add.php?applyid=".$applyid);
}
?>