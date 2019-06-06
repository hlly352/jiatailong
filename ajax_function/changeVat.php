<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$new_data = trim($_POST['new_vat']);
$mould_id = trim($_POST['mould_id']);
//把接受到的数据更新到数据库中
$sql = "UPDATE `db_mould_data` SET `order_vat` = '$new_data' WHERE `mould_dataid`=".$mould_id;

$db->query($sql);
if($db->affected_rows){
	echo 'ok';
}

?>