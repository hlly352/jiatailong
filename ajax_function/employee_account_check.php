<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$account = $_POST['account'];
$sql = "SELECT `account_status` FROM `db_employee` WHERE `account` = '$account'";
$result = $db->query($sql);
if($result->num_rows){
	$array = $result->fetch_assoc();
	$account_status = $array['account_status'];
	if($account_status == 0){
		echo "<img src=\"../images/system_ico/error_10_10.png\" width=\"10\" height=\"10\" /> 账号被关闭#0";
	}else{
		echo "<img src=\"../images/system_ico/right_10_10.png\" width=\"10\" height=\"10\" />#1";
	}
}else{
	echo "<img src=\"../images/system_ico/error_10_10.png\" width=\"10\" height=\"10\" /> 账号不存在#0";
}
?>