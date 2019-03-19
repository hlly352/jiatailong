<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
if($_SERVER['HTTP_REFERER']){
	$pay_amount = $_POST['pay_amount'];
	$linkid = $_POST['linkid'];
	$data_type = $_POST['data_type'];
	$action = $_POST['action'];
	if($data_type == 'M'){
		$sql = "SELECT (`process_cost`+ROUND(`actual_quantity`*`unit_price`,2)) AS `total_amount` FROM `db_material_order_list` WHERE `listid` = '$linkid'";
	}elseif($data_type == 'MO'){
		$sql = "SELECT `cost` AS `total_amount` FROM `db_mould_outward` WHERE `outwardid` = '$linkid'";
	}elseif($data_type == 'MC'){
		$sql = "SELECT (ROUND(`db_cutter_purchase_list`.`quantity`*`db_cutter_order_list`.`unit_price`,2)) AS `total_amount` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_order_list`.`listid` = '$linkid'";
	}
	$result = $db->query($sql);
	if($result->num_rows){
		$array = $result->fetch_assoc();
		$total_amount = $array['total_amount'];
		if($action == "add"){
			$sql_pay_amount = "SELECT SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` = '$linkid' AND `data_type` = '$data_type' GROUP BY `linkid`";
		}elseif($action == "edit"){
			$payid = $_POST['payid'];
			$sql_pay_amount = "SELECT SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` = '$linkid' AND `data_type` = '$data_type' AND `payid` != '$payid' GROUP BY `linkid`";
		}
		$result_pay_amount = $db->query($sql_pay_amount);
		if($result_pay_amount->num_rows){
			$array_pay_amount = $result_pay_amount->fetch_assoc();
			$total_pay_amount = $array_pay_amount['total_pay_amount'];
		}else{
			$total_pay_amount = 0;
		}
		$wait_pay_amount = $total_amount - $total_pay_amount;
		if($pay_amount > $wait_pay_amount){
			echo 0;
		}else{
			echo 1;
		}
	}
}
?>