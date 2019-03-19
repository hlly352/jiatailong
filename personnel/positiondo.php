<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$position_name = trim($_POST['position_name']);
	$position_code = trim($_POST['position_code']);
	if($action == "add"){
		$sql = "INSERT INTO `db_personnel_position` (`positionid`,`position_name`,`position_code`,`position_status`) VALUES (NULL,'$position_name','$position_code',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:position.php");
		}
	}elseif($action == "edit"){
		$positionid = $_POST['positionid'];
		$position_status = $_POST['position_status'];
		$sql = "UPDATE `db_personnel_position` SET `position_name` = '$position_name',`position_code` = '$position_code',`position_status` = '$position_status' WHERE `positionid` = '$positionid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_positionid = $_POST['id'];
		$positionid = fun_convert_checkbox($array_positionid);
		$sql = "DELETE FROM `db_personnel_position` WHERE `positionid` IN ($positionid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>