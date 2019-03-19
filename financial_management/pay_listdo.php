<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	if($action == "add" || $action == "edit"){
		$pay_date = $_POST['pay_date'];
		$pay_amount = $_POST['pay_amount'];
		$data_type = $_POST['data_type'];
		$remark = trim($_POST['remark']);
	}
	if($action == "add"){
		$linkid = $_POST['linkid'];
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
		$sql = "INSERT INTO `db_cash_pay` (`payid`,`pay_date`,`pay_amount`,`linkid`,`data_type`,`employeeid`,`dotime`,`remark`) VALUES (NULL,'$pay_date','$pay_amount','$linkid','$data_type','$employeeid','$dotime','$remark')";
		$db->query($sql);
		if($db->insert_id){
			if($data_type == 'M'){
				header("location:pay_material_order_list.php?id=".$linkid."&action=add");
			}elseif($data_type == 'MO'){
				header("location:pay_mould_outward_list.php?id=".$linkid."&action=add");
			}elseif($data_type == 'MC'){
				header("location:pay_cutter_order_list.php?id=".$linkid."&action=add");
			}
		}
	}elseif($action == "edit"){
		$payid = $_POST['payid'];
		$sql = "UPDATE `db_cash_pay` SET `pay_date` = '$pay_date',`pay_amount` = '$pay_amount',`remark` = '$remark' WHERE `payid` = '$payid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_payid = fun_convert_checkbox($_POST['id']);
		$sql = "DELETE FROM `db_cash_pay` WHERE `payid` IN ($array_payid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>