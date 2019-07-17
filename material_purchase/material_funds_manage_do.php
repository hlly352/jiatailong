<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = fun_check_action();
$accountid = $_GET['id'];
if($action == 'approval'){
   //更改对账状态
	$inout_sql = "UPDATE `db_material_inout` SET `account_status` = 'A' WHERE `inoutid` IN(SELECT `inoutid` FROM `db_material_account_list` WHERE `accountid` = '$accountid')";
	echo $inout_sql;
	$db->query($inout_sql);
	if($db->affected_rows){
		header('location:'.$_SERVER['HTTP_REFERER']);
			}
}

?>