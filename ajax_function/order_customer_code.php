<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$customer_id = trim($_POST['customer_id']);
$sql = "SELECT `customer_code` FROM `db_customer_info` WHERE `customer_id` = ".$customer_id;
$result = $db->query($sql);
if($result->num_rows){
	$customer_code = $result->fetch_row()[0];
	echo  strstr($customer_code,'$$')?substr($customer_code,strrpos($customer_code,'$$')+2):$customer_code;
}
?>