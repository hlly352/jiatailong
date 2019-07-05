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
	$taker = trim($_POST['taker']);
	$remark = trim($_POST['remark']);
	if($action == "add"){
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
		// $sql_list = "SELECT * FROM `db_material_order_list` WHERE `listid` = '$listid' AND `order_surplus` >= $quantity";
		// $result_list = $db->query($sql_list);
		// if($result_list->num_rows){
		// 	$sql_update = "UPDATE `db_material_order_list` SET `order_surplus` = `order_surplus` - '$quantity' WHERE `listid` = '$listid'";
		// 	$db->query($sql_update);
		$inoutid = $_POST['inoutid'];
			//if($db->affected_rows){
				$sql = "INSERT INTO `db_other_material_inout` (`inoutid`,`dodate`,`dotype`,`form_number`,`actual_quantity`,`taker`,`remark`,`listid`,`employeeid`,`dotime`) VALUES (NULL,'$dodate','O','$form_number','$actual_quantity','$taker','$remark','$listid`','$employeeid','$dotime')";
				$db->query($sql);
				if($db->insert_id){
					$update_sql = "UPDATE `db_other_material_inout` SET `inout_quantity` = `inout_quantity` - $actual_quantity WHERE `inoutid` =".$inoutid;
					$db->query($update_sql);
					header("location:other_inout_list_out.php");
				//}
			//}
		}
	}elseif($action == "edit"){
		$inoutid = $_POST['inoutid'];
		$sql = "UPDATE `db_material_inout` SET `dodate` = '$dodate',`form_number` = '$form_number',`taker` = '$taker',`remark` = '$remark' WHERE `inoutid` = '$inoutid'";
		$resutl = $db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>