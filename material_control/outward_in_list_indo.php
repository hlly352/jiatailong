<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = $_POST['action'];
if($action == 'query'){
	$orderid = $_POST['orderid'];
	$taker = $_POST['taker'];
	$form_number = $_POST['form_number'];
	$dodate = $_POST['dodate'];
	$remark = $_POST['remark'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	//查询当前订单所包含的明细id
	$sql_list = "SELECT `listid` FROM `db_outward_order_list` WHERE `orderid` = '$orderid'";
	$result_list = $db->query($sql_list);
	if($result_list->num_rows){
		$sql_str = '';
		while($row_list = $result_list->fetch_assoc()){
			$listid = $row_list['listid'];
			$sql_str .= '(\''.$form_number.'\',\''.$dodate.'\',\''.$taker.'\',\''.$remark.'\',\''.$listid.'\',\''.$employeeid.'\'),';
		}
	}
	$sql_str = rtrim($sql_str,',');
	//外协信息插入到外协入库表中
	$sql_inout = "INSERT INTO `db_outward_inout`(`form_number`,`dodate`,`taker`,`remark`,`listid`,`employeeid`) VALUES $sql_str";
	
	$db->query($sql_inout);
	//更改外协订单状态
	$sql = "UPDATE `db_outward_order` SET `order_status` = '2' WHERE `orderid` = '$orderid'";
	$db->query($sql);
	if($db->affected_rows){
		header('location:outward_in_list.php');
	}
}
?>