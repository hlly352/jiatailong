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
$apply_amount = trim($_POST['apply_amount']);
$accountid = trim($_POST['accountid']);

if($action == 'edit'){
		$fundsid = $_GET['id'];
		//更改付款状态
		$sql = "UPDATE `db_material_funds_list` SET `approval_status` = 'Z' WHERE `fundsid` = '$fundsid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:material_funds_summary.php');
		}
	}
?>