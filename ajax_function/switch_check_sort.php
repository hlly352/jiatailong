<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$id = trim($_POST['id']);
$sort = htmlspecialchars(trim($_POST['sort']));
//更改设计评审项目的排序
$sql = "UPDATE `db_mould_check_data` SET `sort` = '$sort' WHERE `id` = '$id'";
$db->query($sql);
if($db->affected_rows){
	echo '1';
}else{
	echo '0';
}
?>