<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$listid_array = $_POST['id'];
	$listid = fun_convert_checkbox($listid_array);
	if($action == "del"){
		$sql = "DELETE FROM `db_outdown_list` WHERE `listid` IN($listid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}