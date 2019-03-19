<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_listid = $_POST['listid'];
	$array_dodate = $_POST['dodate'];
	$array_form_number = $_POST['form_number'];
	$array_quantity = $_POST['quantity'];
	$array_taker = $_POST['taker'];
	$array_remark = $_POST['remark'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	foreach($array_listid as $key=>$listid){
		$dodate = $array_dodate[$key];
		$form_number = trim($array_form_number[$key]);
		$quantity = $array_quantity[$key];
		$taker = trim($array_taker[$key]);
		$remark = trim($array_remark[$key]);
		if($taker){
			$sql_list = "SELECT * FROM `db_material_order_list` WHERE `listid` = '$listid' AND `order_surplus` >= $quantity";
			$result_list = $db->query($sql_list);
			if($result_list->num_rows){
				$sql_add .= "(NULL,'$dodate','O','$form_number','$quantity','$taker','$remark','$listid`','$employeeid','$dotime'),";
				$sql_update = "UPDATE `db_material_order_list` SET `order_surplus` = `order_surplus` - '$quantity' WHERE `listid` = '$listid'";
				$db->query($sql_update);
			}
		}
	}
	$sql_add = rtrim($sql_add,',');
	$sql = "INSERT INTO `db_material_inout` (`inoutid`,`dodate`,`dotype`,`form_number`,`quantity`,`taker`,`remark`,`listid`,`employeeid`,`dotime`) VALUES $sql_add";
	$db->query($sql);
	if($db->insert_id){
		header("location:material_inout_list_out.php");
	}
}
?>