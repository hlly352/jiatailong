<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';

if($_POST['submit']){
	$action = $_POST['action'];
	$dodate = $_POST['dodate'];
	$form_number = trim($_POST['form_number']);
 	$remark = trim($_POST['remark']);
	if($action == "add"){
		$quantity = $_POST['quantity'];
		$listid = $_POST['listid'];
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
		//
		// $sql_surplus = "SELECT `db_cutter_purchase_list`.`cutterid`,SUM(`db_cutter_order_list`.`surplus`) AS `surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`cutterid` IN ($array_cutterid) AND `db_cutter_order_list`.`surplus` > 0 GROUP BY `db_cutter_purchase_list`.`cutterid`";
		//查询刀具的期初库存
		echo $listid;
		$start_cutter_sql = "SELECT SUM(`db_cutter_order_list`.`surplus`) AS `start_quantity` FROM `db_cutter_purchase_list` INNER JOIN `db_cutter_order_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`cutterid` = (SELECT `db_cutter_purchase_list`.`cutterid` FROM `db_cutter_order_list` LEFT JOIN `db_cutter_purchase_list` ON `db_cutter_order_list`.`purchase_listid` = `db_cutter_purchase_list`.`purchase_listid` WHERE `db_cutter_order_list`.`listid`='$listid' AND `db_cutter_order_list`.`surplus` >= 0) AND `db_cutter_purchase_list`.`cutterid`";
	
		$res_start_cutter = $db->query($start_cutter_sql);
		if($res_start_cutter->num_rows){
			$row_start_cutter = $res_start_cutter->fetch_assoc();
		}
		//期初库存
		$start_quantity = intval($row_start_cutter['start_quantity']);
		//计算期末库存
		$end_quantity = $row_start_cutter['start_quantity'] + $quantity;
	
		$sql_list = "SELECT `db_cutter_purchase_list`.`cutterid` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_order_list`.`listid` = '$listid' AND (`db_cutter_purchase_list`.`quantity`-`db_cutter_order_list`.`in_quantity`) >= '$quantity'";
		$result_list = $db->query($sql_list);
		if($result_list->num_rows){
			$array_list = $result_list->fetch_assoc();
			$cutterid = $array_list['cutterid'];
			$sql_update = "UPDATE `db_cutter_order_list` SET `in_quantity` = `in_quantity` + '$quantity',`surplus` = `surplus` + '$quantity' WHERE `listid` = '$listid'";
			$db->query($sql_update);
			if($db->affected_rows){
				$sql = "INSERT INTO `db_cutter_inout` (`inoutid`,`listid`,`dotype`,`form_number`,`dodate`,`quantity`,`employeeid`,`remark`,`dotime`,`start_quantity`,`end_quantity`) VALUES (NULL,'$listid`','I','$form_number','$dodate','$quantity','$employeeid','$remark','$dotime','$start_quantity','$end_quantity')";
				$db->query($sql);
				if($db->insert_id){
					header("location:cutter_inout_list_in.php");
				}
			}
		}
	}elseif($action == "edit"){
		$inoutid = $_POST['inoutid'];
		$sql = "UPDATE `db_cutter_inout` SET `dodate` = '$dodate',`form_number` = '$form_number',`remark` = '$remark' WHERE `inoutid` = '$inoutid'";
		$resutl = $db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>