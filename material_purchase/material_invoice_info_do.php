<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
//获取数据
$invoice_no = $_POST['invoice_no'];
$invoice_amount = $_POST['invoice_amount'];
$accountid = $_POST['accountid'];
$invoice_date = $_POST['invoice_date'];
$employeeid = $_POST['employeeid'];
$action = fun_check_action();
if($action == 'add'){
	$sql = "INSERT INTO `db_material_invoice_list`(`accountid`,`invoice_no`,`amount`,`date`,`employeeid`) VALUES('$accountid','$invoice_no','$invoice_amount','$invoice_date','$employeeid')";
	echo $sql;
	$db->query($sql);
	if($db->affected_rows){
		$invoiceid = $db->insert_id;
		//把发票id存入对账汇总表中
		$account_sql = "UPDATE `db_material_account` SET `invoiceid` = '$invoiceid' WHERE `accountid` = ".$accountid;
		$db->query($account_sql);
		if($db->affected_rows){
			//更改对账状态
			$inout_sql = "UPDATE `db_material_inout` SET `account_status` = 'M' WHERE `inoutid` IN(SELECT `inoutid` FROM `db_material_account_list` WHERE `accountid` = '$accountid')";
			$db->query($inout_sql);
			if($db->affected_rows){
				header('location:material_invoice_manage.php');
			}
		}

	}
}

?>