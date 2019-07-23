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
if($action == 'add'){
	foreach($invoice_no as $k=>$v){
	$sql = "INSERT INTO `db_material_invoice_list`(`accountid`,`invoice_no`,`amount`,`date`,`employeeid`) VALUES('$accountid','$invoice_no[$k]','$invoice_amount[$k]','$invoice_date[$k]','$employeeid')";
	echo $sql;
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