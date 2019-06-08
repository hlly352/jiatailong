<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$password = md5($_POST['password'].ALL_PW);
$employeeid = $_POST['employeeid'];
$sql = "SELECT `password` FROM `db_employee` WHERE `employeeid` = '$employeeid' AND `password` = '$password'";
$result = $db->query($sql);
if($result->num_rows){
	echo "<img src=\"../images/system_ico/right_10_10.png\" width=\"10\" height=\"10\" />";
}else{
	echo "<img src=\"../images/system_ico/error_10_10.png\" width=\"10\" height=\"10\" /> 密码错误";
}

?>
