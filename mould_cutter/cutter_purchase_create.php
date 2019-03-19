<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
//自动生成申购单号
$sql_number = "SELECT MAX((SUBSTRING(`purchase_number`,-4)+0)) AS `max_number` FROM `db_cutter_purchase` WHERE DATE_FORMAT(`purchase_date`,'%Y') = YEAR(NOW())";
$result_number = $db->query($sql_number);
if($result_number->num_rows){
	$array_number = $result_number->fetch_assoc();
	$max_number = $array_number['max_number'];
	$next_number = $max_number+1;
	$purchase_number = 'C'.date('Y').strtolen($next_number,4).$next_number;
}else{
	$purchase_number = 'C'.date('Y').'0001';
}
$purchase_date = fun_getdate();
$purchase_time = fun_gettime();
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql = "INSERT INTO `db_cutter_purchase` (`purchaseid`,`purchase_number`,`purchase_date`,`purchase_time`,`employeeid`) VALUES (NULL,'$purchase_number','$purchase_date','$purchase_time','$employeeid')";
$db->query($sql);
if($purchaseid = $db->insert_id){
	header("location:cutter_purchase_list.php?purchaseid=".$purchaseid);
}
?>