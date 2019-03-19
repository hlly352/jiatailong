<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$typeid = $_POST['typeid'];
	$specification = trim($_POST['specification']);
	if($action == "add"){
		$sql = "INSERT INTO `db_cutter_specification` (`specificationid`,`specification`,`typeid`) VALUES (NULL,'$specification','$typeid')";
		$db->query($sql);
		if($db->insert_id){
			header("location:cutter_specification.php");
		}
	}elseif($action == "edit"){
		$specificationid = $_POST['specificationid'];
		$sql = "UPDATE `db_cutter_specification` SET `typeid` = '$typeid',`specification` = '$specification' WHERE `specificationid` = '$specificationid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}
	}elseif($action == "del"){
		$array_specificationid = fun_convert_checkbox($_POST['id']);
		$sql = "DELETE FROM `db_cutter_specification` WHERE `specificationid` IN ($array_specificationid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>