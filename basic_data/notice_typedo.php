<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$notice_typename = trim($_POST['notice_typename']);
	if($action == "add"){
		$sql = "INSERT INTO `db_notice_type` (`notice_typeid`,`notice_typename`,`notice_typestatus`) VALUES (NULL,'$notice_typename',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:notice_type.php");
		}
	}elseif($action == "edit"){
		$notice_typeid = $_POST['notice_typeid'];
		$notice_typestatus = $_POST['notice_typestatus'];
		$sql = "UPDATE `db_notice_type` SET `notice_typename` = '$notice_typename',`notice_typestatus` = '$notice_typestatus'  WHERE `notice_typeid` = '$notice_typeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$notice_typeid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_notice_type` WHERE `notice_typeid` IN ($notice_typeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>