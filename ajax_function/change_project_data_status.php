<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$showid = $_POST['showid'];
//更改资料的查看状态
$sql = "UPDATE `db_mould_data_show` SET `status` = '1' WHERE `id` = '$showid'";

$result = $db->query($sql);
if($db->affected_rows){
	echo '1';
}else{
	echo '0';
}
?>