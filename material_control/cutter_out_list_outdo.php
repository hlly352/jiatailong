<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	if($action == "add"){
		$apply_listid = $_POST['apply_listid'];
		$array_listid = $_POST['listid'];
		$array_quantity = $_POST['quantity'];
		$array_old_quantity = $_POST['old_quantity'];
		$array_dodate = $_POST['dodate'];
		$array_remark = $_POST['remark'];
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
		foreach($array_listid as $key=>$listid){
			$quantity = $array_quantity[$key];
			$old_quantity = $array_old_quantity[$key];
			$dodate = $array_dodate[$key];
			$remark = trim($array_remark[$key]);
			if($quantity){
				$sql_surplus = "SELECT * FROM `db_cutter_order_list` WHERE `listid` = '$listid' AND `surplus` >= '$quantity'";
				$result_surplus = $db->query($sql_surplus);
				if($result_surplus->num_rows){
					$sql_add = "INSERT INTO `db_cutter_inout` (`inoutid`,`listid`,`apply_listid`,`dotype`,`quantity`,`old_quantity`,`dodate`,`employeeid`,`remark`,`dotime`) VALUES (NULL,'$listid','$apply_listid','O','$quantity','$old_quantity','$dodate','$employeeid','$remark','$dotime')";
					$db->query($sql_add);
					if($db->insert_id){
						$total_out_quantity += $quantity;
						$sql_update = "UPDATE `db_cutter_order_list` SET `surplus` = `surplus` - '$quantity' WHERE `listid` = '$listid'";
						$db->query($sql_update);
					}
				}
			}
		}
		$sql_update = "UPDATE `db_cutter_apply_list` SET `out_quantity` = `out_quantity` + '$total_out_quantity' WHERE `apply_listid` = '$apply_listid'";
		$db->query($sql_update);
		if($db->affected_rows){
			header("location:cutter_inout_list_out.php");
		}
	}elseif($action == "edit"){
		$inoutid = $_POST['inoutid'];
		$old_quantity = $_POST['old_quantity'];
		$dodate = $_POST['dodate'];
		$remark = trim($_POST['remark']);
		$sql = "UPDATE `db_cutter_inout` SET `old_quantity` = '$old_quantity',`dodate` = '$dodate',`remark` = '$remark' WHERE `inoutid` = '$inoutid'";
		$resutl = $db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>