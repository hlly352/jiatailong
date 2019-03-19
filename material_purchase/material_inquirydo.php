<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_materialid = $_POST['id'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	foreach($array_materialid as $materialid){
		$sqladd .= "(NULL,'$materialid','$employeeid'),";
	}
	$sqladd = rtrim($sqladd,',');
	$sql = "INSERT INTO `db_material_inquiry` (`inquiryid`,`materialid`,`employeeid`) values ".$sqladd;
	$db->query($sql);
	if($db->insert_id){
		header("location:material_inquiry_list.php");
	}
}
?>