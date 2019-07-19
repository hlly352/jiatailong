<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_SERVER['HTTP_REFERER']){
	$specificationid = $_POST['specificationid'];
	$hardnessid = $_POST['hardnessid'];
	$sql_surplus = "SELECT `db_cutter_order_list`.`surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchae_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` WHERE `db_mould_cutter`.`specificationid` = '$specificationid' AND `db_mould_cutter`.`hardnessid` = '$hardnessid' AND `db_cutter_order_list`.`surplus` > 0";
	$result_surplus = $db->query($sql_surplus);
	if($result_surplus->num_rows){
		while($array_surplus = $result_surplus->fetch_assoc()){
		$surplus += $array_surplus['surplus'];
		}
	}else{
		$surplus = 0;
	}
	echo $surplus;
}
?>