<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$account = trim($_POST['account']);
$employeeid = $_POST['employeeid'];
$sql_account = "SELECT * FROM `db_employee` WHERE `account` = '$account' AND `employeeid` != '$employeeid'";
$result_account = $db->query($sql_account);
if($result_account->num_rows){
	echo "账号已存在，请重新输入！";
}
?>