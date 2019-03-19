<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$try_causename = trim($_POST['try_causename']);
	if($action == "add"){
		$sql = "INSERT INTO `db_mould_try_cause` (`try_causeid`,`try_causename`,`try_causestatus`) VALUES (NULL,'$try_causename',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:mould_try_cause.php");
		}
	}elseif($action == "edit"){
		$try_causeid = $_POST['try_causeid'];
		$try_causestatus = $_POST['try_causestatus'];
		$sql = "UPDATE `db_mould_try_cause` SET `try_causename` = '$try_causename',`try_causestatus` = '$try_causestatus'  WHERE `try_causeid` = '$try_causeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$try_causeid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_mould_try_cause` WHERE `try_causeid` IN ($try_causeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>