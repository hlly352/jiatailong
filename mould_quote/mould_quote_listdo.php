<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$mould_dataid = $_POST['mould_dataid'];
	$array_quoteid = fun_convert_checkbox($_POST['id']);
	$sql_list = "DELETE FROM `db_mould_quote_list` WHERE `quoteid` IN ($array_quoteid)";
	$db->query($sql_list);
	$sql = "DELETE FROM `db_mould_quote` WHERE `quoteid` IN ($array_quoteid)";
	$db->query($sql);
	header("location:".$_SERVER['HTTP_REFERER']);
}
?>