<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$orderid = trim($_POST['orderid']);

$sql = "UPDATE `db_outward_order` SET `order_status` = IF(`order_status` = '1','0','1') WHERE `orderid` = '$orderid'";
$db->query($sql);
if($db->affected_rows){
	echo '1';
}else{
	echo '0';
}
?>