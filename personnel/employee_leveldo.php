<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_employeeid = $_POST['employeeid'];
	$employeeid = fun_convert_checkbox($array_employeeid);
	$superior = $_POST['superior'];
	$sql = "UPDATE `db_employee` SET `superior` = '$superior' WHERE `employeeid` IN ($employeeid)";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>