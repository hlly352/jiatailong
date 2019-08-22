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
$i = 0;
//发票信息插入到账款管理表中
$invoice_list = fun_convert_checkbox($invoice_no);
$funds_sql = "UPDATE `db_material_funds_list` SET `invoice_no` = '$invoice_list' WHERE `accountid` = '$accountid'";
$db->query($funds_sql);
if($action == 'add'){
	foreach($invoice_no as $k=>$v){
	$sql = "INSERT INTO `db_material_invoice_list`(`accountid`,`invoice_no`,`amount`,`date`,`employeeid`) VALUES('$accountid','$invoice_no[$k]','$invoice_amount[$k]','$invoice_date[$k]','$employeeid')";
	
	$db->query($sql);
	if(!$db->affected_rows){
			$i++;
		}
	}
		if($i == 0){
			header('location:material_invoice_manage.php');
			
		}
}

?>   