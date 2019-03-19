<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$quantity = $_POST['quantity'];
$listid = $_POST['listid'];
$sql = "SELECT * FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_order_list`.`listid` = '$listid' AND (`db_cutter_purchase_list`.`quantity`-`db_cutter_order_list`.`in_quantity`) >= '$quantity'";
$result = $db->query($sql);
if($result->num_rows){
	echo 1;
}else{
	echo 0;
}
?>