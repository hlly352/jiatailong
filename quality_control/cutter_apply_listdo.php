<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_listid = fun_convert_checkbox($_POST['id']);
	$sql = "DELETE FROM `db_cutter_apply_list` WHERE `apply_listid` IN ($array_listid)";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>