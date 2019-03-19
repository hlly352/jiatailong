<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_purchase_listid = $_POST['id'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	foreach($array_purchase_listid as $purchase_listid){
		$sqladd .= "(NULL,'$purchase_listid','$employeeid'),";
	}
	$sqladd = rtrim($sqladd,',');
	$sql = "INSERT INTO `db_cutter_inquiry` (`inquiryid`,`purchase_listid`,`employeeid`) values ".$sqladd;
	$db->query($sql);
	if($db->insert_id){
		header("location:cutter_inquiry_list.php");
	}
}
?>