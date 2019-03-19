<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$dept_name = trim($_POST['dept_name']);
	$dept_order = $_POST['dept_order'];
	if($action == "add"){
		$sql = "INSERT INTO `db_department` (`deptid`,`dept_name`,`dept_order`,`dept_status`) VALUES (NULL,'$dept_name','$dept_order',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:department.php");
		}
	}elseif($action == "edit"){
		$deptid = $_POST['deptid'];
		$dept_status = $_POST['dept_status'];
		$sql = "UPDATE `db_department` SET `dept_name` = '$dept_name',`dept_order` = '$dept_order',`dept_status` = '$dept_status' WHERE `deptid` = '$deptid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_deptid = $_POST['id'];
		$deptid = fun_convert_checkbox($array_deptid);
		$sql = "DELETE FROM `db_department` WHERE `deptid` IN ($deptid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>