<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$mould_statusname = trim($_POST['mould_statusname']);
	if($action == "add"){
		$sql = "INSERT INTO `db_mould_status` (`mould_statusid`,`mould_statusname`) VALUES (NULL,'$mould_statusname')";
		$db->query($sql);
		if($db->insert_id){
			header("location:mould_status.php");
		}
	}elseif($action == "edit"){
		$mould_statusid = $_POST['mould_statusid'];
		$sql = "UPDATE `db_mould_status` SET `mould_statusname` = '$mould_statusname' WHERE `mould_statusid` = '$mould_statusid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$mould_statusid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_mould_status` WHERE `mould_statusid` IN ($mould_statusid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>