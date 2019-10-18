<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_materialid = $_POST['id'];
	$inquiry_date = date('Y-m-d');
	$employeeid = $_SESSION['employee_info']['employeeid'];
	//生成一个询价单
	//生成询价单号
	$sql_number = "SELECT MAX((SUBSTRING(`inquiry_number`,-2)+0)) AS `max_number` FROM `db_material_inquiry_order` WHERE DATE_FORMAT(`inquiry_date`,'%Y-%m-%d') = '$inquiry_date'";
		$result_number = $db->query($sql_number);
		if($result_number->num_rows){
			$array_number = $result_number->fetch_assoc();
			$max_number = $array_number['max_number'];
			$next_number = $max_number + 1;
			$inquiry_number = date('Ymd',strtotime($inquiry_date)).strtolen($next_number,2).$next_number;
		}else{
			$inquiry_number = date('Ymd',strtotime($inquiry_date))."01";
		} 
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
	$sql_inquiry_order = "INSERT INTO `db_material_inquiry_order`(`inquiry_number`,`inquiry_date`,`dotime`,`employeeid`) VALUES('$inquiry_number','$inquiry_date','$dotime','$employeeid')";
	$db->query($sql_inquiry_order);
	$inquiry_orderid = $db->insert_id;

	foreach($array_materialid as $materialid){
		$sqladd .= "('$materialid','$inquiry_orderid','$employeeid'),";
	}
	$sqladd = rtrim($sqladd,',');
	$sql = "INSERT INTO `db_material_inquiry_orderlist` (`materialid`,`inquiry_orderid`,`employeeid`) values ".$sqladd;
	$db->query($sql);
	if($db->insert_id){
		header("location:material_inquiry_order.php");
	}
}
?>