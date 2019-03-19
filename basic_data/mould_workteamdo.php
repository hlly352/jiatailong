<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$workteam_name = trim($_POST['workteam_name']);
	if($action == "add"){
		$sql = "INSERT INTO `db_mould_workteam` (`workteamid`,`workteam_name`,`workteam_status`) VALUES (NULL,'$workteam_name',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:mould_workteam.php");
		}
	}elseif($action == "edit"){
		$workteamid = $_POST['workteamid'];
		$workteam_status = $_POST['workteam_status'];
		$sql = "UPDATE `db_mould_workteam` SET `workteam_name` = '$workteam_name',`workteam_status` = '$workteam_status'  WHERE `workteamid` = '$workteamid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$workteamid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_mould_workteam` WHERE `workteamid` IN ($workteamid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>