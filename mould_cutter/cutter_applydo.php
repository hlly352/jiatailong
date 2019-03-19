<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_applyid = fun_convert_checkbox($_POST['id']);
	$sql_list = "DELETE FROM `db_cutter_apply_list` WHERE `applyid` IN ($array_applyid)";
	$db->query($sql_list);
	$sql = "DELETE FROM `db_cutter_apply` WHERE `applyid` IN ($array_applyid)";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>