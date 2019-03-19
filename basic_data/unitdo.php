<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$unit_name = trim($_POST['unit_name']);
	if($action == "add"){
		$sql = "INSERT INTO `db_unit` (`unitid`,`unit_name`) VALUES (NULL,'$unit_name')";
		$db->query($sql);
		if($db->insert_id){
			header("location:unit.php");
		}
	}elseif($action == "edit"){
		$unitid = $_POST['unitid'];
		$sql = "UPDATE `db_unit` SET `unit_name` = '$unit_name' WHERE `unitid` = '$unitid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$unitid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_unit` WHERE `unitid` IN ($unitid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>