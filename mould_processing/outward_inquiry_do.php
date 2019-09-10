<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = $_POST['action'];
if($_POST['submit']){
	if($action == 'add'){
		$outward_typeid = $_POST['outward_typeid'];
		$array_materialid = $_POST['id'];
		$str_materialid = fun_convert_checkbox($array_materialid);
		$employeeid = $_SESSION['employee_info']['employeeid'];
		foreach($array_materialid as $materialid){
			$sqladd .= "(NULL,'$materialid','$employeeid'),";
		}
		$outward_type_sql = "UPDATE `db_mould_material` SET `outward_typeid` = '$outward_typeid' WHERE `materialid` IN($str_materialid)";
		$db->query($outward_type_sql);
		$sqladd = rtrim($sqladd,',');
		$sql = "INSERT INTO `db_outward_inquiry` (`inquiryid`,`materialid`,`employeeid`) values ".$sqladd;
		$db->query($sql);
		if($db->insert_id){
			header("location:outward_inquiry_list.php");
		}
	}elseif($action == 'del'){
		$array_materialid = $_POST['id'];
		$materialid = fun_convert_checkbox($array_materialid);
		
		$sql = "DELETE FROM `db_outward_inquiry` WHERE `inquiryid` IN($materialid)";
		$db->query($sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}
}
?>