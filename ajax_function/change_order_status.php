<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$orderid = trim($_POST['orderid']);
//把接受到的数据更新到数据库中
$sql = "UPDATE `db_material_order` SET `order_status` = IF(`order_status` = '0','1',0) WHERE `orderid` = '$orderid'";

$db->query($sql);
if($db->affected_rows){
	echo 'ok';
}

?>