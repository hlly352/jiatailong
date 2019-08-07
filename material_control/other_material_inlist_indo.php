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
	$actual_quantity = $_POST['actual_quantity'];
	$inout_quantity = trim($_POST['inout_quantity']);
	$amount = $_POST['amount'];
 	$remark = trim($_POST['remark']);
 	$mould_other_id = $_POST['mould_other_id'];
 	$dataid = trim($_POST['dataid']);
	if($action == "add"){
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
		// $sql_list = "SELECT * FROM `db_material_order_list` WHERE `listid` = '$listid' AND (`order_quantity`-`in_quantity`) >= $quantity";
		// $result_list = $db->query($sql_list);
		// if($result_list->num_rows){
		// 	$sql_update = "UPDATE `db_material_order_list` SET `in_quantity` = `in_quantity` + '$quantity',`order_surplus` = `order_surplus` + '$quantity' WHERE `listid` = '$listid'";
		// 	$db->query($sql_update);
		// 	if($db->affected_rows){
		//更改库存量
		$data_sql = "UPDATE `db_other_material_data` SET `stock` = `stock` + '$inout_quantity' WHERE `dataid` = '$dataid'";
		$db->query($data_sql);
		//更改入库数量
		$order_list_sql = "UPDATE `db_other_material_orderlist` SET `in_quantity` = `in_quantity` + '$inout_quantity' WHERE `listid` = '$listid'";
		$db->query($order_list_sql);
				$sql = "INSERT INTO `db_other_material_inout` (`inoutid`,`dodate`,`dotype`,`form_number`,`actual_quantity`,`inout_quantity`,`amounts`,`remark`,`listid`,`employeeid`,`dotime`) VALUES (NULL,'$dodate','I','$form_number','$actual_quantity','$inout_quantity','$amount','$remark','$listid`','$employeeid','$dotime')";
				$db->query($sql);
				if($db->insert_id){
					$update_sql = "UPDATE `db_mould_other_material` SET `status`='F' WHERE `mould_other_id`=".$mould_other_id;
					$db->query($update_sql);
					if($db->affected_rows){

					header("location:other_inout_list_in.php");
					}
				}
		// 	}
		// }
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