 <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$listid = $_POST['listid'];
	$dodate = $_POST['dodate'];
	$form_number = trim($_POST['form_number']);
	$quantity = $_POST['quantity'];
	$inout_quantity = $_POST['inout_quantity'];
	$amount = $_POST['amount'];
	$process_cost = $_POST['process_cost'];
 	$remark = trim($_POST['remark']);
	if($action == "add"){
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
		$sql_list = "SELECT * FROM `db_material_order_list` WHERE `listid` = '$listid' AND (`order_quantity`-`in_quantity`) >= $quantity";
		$result_list = $db->query($sql_list);
		if($result_list->num_rows){
			$sql_update = "UPDATE `db_material_order_list` SET `in_quantity` = `in_quantity` + '$quantity',`order_surplus` = `order_surplus` + '$quantity' WHERE `listid` = '$listid'";
			$db->query($sql_update);
			if($db->affected_rows){
				$sql = "INSERT INTO `db_material_inout` (`inoutid`,`dodate`,`dotype`,`form_number`,`quantity`,`inout_quantity`,`amount`,`process_cost`,`remark`,`listid`,`employeeid`,`dotime`) VALUES (NULL,'$dodate','I','$form_number','$quantity','$inout_quantity','$amount',`process_cost`,'$remark','$listid`','$employeeid','$dotime')";
				$db->query($sql);
				if($db->insert_id){
					header("location:material_inout_list_in.php");
				}
			}
		}
	}elseif($action == "edit"){
		$inoutid = $_POST['inoutid'];
		$sql = "UPDATE `db_material_inout` SET `dodate` = '$dodate',`form_number` = '$form_number',`inout_quantity` = '$inout_quantity',`amount` = '$amount',`process_cost` = '$process_cost',`remark` = '$remark' WHERE `inoutid` = '$inoutid'";
		$resutl = $db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>