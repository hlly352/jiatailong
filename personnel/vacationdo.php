<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$vacation_name = trim($_POST['vacation_name']);
	if($action == 'add'){
		$sql = "INSERT INTO `db_personnel_vacation` (`vacationid`,`vacation_name`,`vacation_status`) VALUES (NULL,'$vacation_name',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:vacation.php");
		}
	}elseif($action == 'edit'){
		$vacationid = $_POST['vacationid'];
		$vacation_status = $_POST['vacation_status'];
		$sql = "UPDATE `db_personnel_vacation` SET `vacation_name` = '$vacation_name',`vacation_status` = '$vacation_status' WHERE `vacationid` = '$vacationid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == 'del'){
		$array_id = $_POST['id'];
		$vacationid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_personnel_vacation` WHERE `vacationid` IN ($vacationid)";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>