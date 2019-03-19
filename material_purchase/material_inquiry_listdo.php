<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_inquiryid = $_POST['id'];
	$inquiryid = fun_convert_checkbox($array_inquiryid);
	$sql = "DELETE FROM `db_material_inquiry` WHERE `inquiryid` IN ($inquiryid)";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>