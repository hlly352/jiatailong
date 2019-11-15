<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$designid = trim($_POST['designid']);
//查询当前模具的历史计划
$sql = "UPDATE `db_design_plan` SET `is_approval` = '1' WHERE `designid` = '$designid'";

$result = $db->query($sql);
if($result->affected_rows){
	echo '1';
}else{
	echo '0';
}
?>