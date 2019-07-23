
























<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
//获取数据
$action = fun_check_action();
$accountid = $_GET['id'];
if($action == 'edit'){
	//更改物料的对账状态
	$inout_sql = "UPDATE `db_material_inout` SET `account_status` = 'M' WHERE `inoutid` IN(SELECT `inoutid` FROM `db_material_account_list` WHERE `accountid` = '$accountid')";
	$db->query($inout_sql);
	if($db->affected_rows){
		//更改发票的接收状态
		$invoice_sql = "UPDATE `db_material_invoice_list` SET `status` = 'C' WHERE `accountid` =$accountid";
		$db->query($invoice_sql);
		if($db->affected_rows){
			header('location:material_invoice_manage.php');
		}
	}
}

?>