<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$quantity = $_POST['quantity'];
$cutterid = $_POST['cutterid'];
$sql_surplus = "SELECT `db_cutter_order_list`.`surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`cutterid` = '$cutterid' AND `db_cutter_order_list`.`surplus` > 0";
$result_surplus = $db->query($sql_surplus);
if($result_surplus->num_rows){
	while($row_surplus = $result_surplus->fetch_assoc()){
		$surplus += $row_surplus['surplus'];
	}
}else{
	$surplus = 0;
}
if($surplus >= $quantity){
	echo 1;
}else{
	echo 0;
}
?>