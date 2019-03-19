<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$quantity = $_POST['quantity'];
$listid = $_POST['listid'];
$sql = "SELECT * FROM `db_cutter_order_list` WHERE `listid` = '$listid' AND `surplus` >= '$quantity'";
$result = $db->query($sql);
if($result->num_rows){
	echo 1;
}else{
	echo 0;
}
?>