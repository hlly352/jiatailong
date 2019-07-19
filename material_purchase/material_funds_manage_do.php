<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = fun_check_action();
$accountid = $_POST['accountid'];
$apply_amount = floatval(trim($_POST['apply_amount']));
$invoice_no = trim($_POST['invoice_no']);
$actual_amount = trim($_POST['actual_amount']);
$accountid = trim($_POST['accountid']);
$amount = trim($_POST['amount']);
$supplierid = $_POST['supplierid'];

if($action == 'approval'){
   //添加申请金额
   $apply_sql = "UPDATE `db_material_account` SET `apply_amount` = `apply_amount` + '$actual_amount' WHERE `accountid` = '$accountid'";

   $db->query($apply_sql);
   if($db->affected_rows){
   //更改对账状态
	$inout_sql = "UPDATE `db_material_inout` SET `account_status` = 'A' WHERE `inoutid` IN(SELECT `inoutid` FROM `db_material_account_list` INNER JOIN `db_material_account` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` WHERE `db_material_account`.`accountid` = '$accountid' AND (`db_material_account`.`apply_amount` - `db_material_account`.`amount`) >= 0)";
	$db->query($inout_sql);
	$date = date('Y-m-d');
	$funds_sql = "INSERT INTO `db_material_funds_list`(`accountid`,`approval_date`,`amount`,`apply_amount`,`employeeid`,`invoice_no`,`supplierid`,`remark`) VALUES('$accountid','$date','$amount','$actual_amount','$employeeid','$invoice_no','$supplierid','$remark')";
	$db->query($funds_sql);
	if($db->affected_rows){
	  header('location:material_funds_manage.php');
	  		}
		}
	} elseif($action == 'edit'){
		$fundsid = $_GET['id'];
		//更改付款状态
		$sql = "UPDATE `db_material_funds_list` SET `approval_status` = 'Y' WHERE `fundsid` = '$fundsid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}
?>