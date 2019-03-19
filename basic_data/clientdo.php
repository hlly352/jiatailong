<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$client_code = trim($_POST['client_code']);
	$client_cname = trim($_POST['client_cname']);
	$client_ename = trim($_POST['client_ename']);
	$client_name = trim($_POST['client_name']);
	$client_address = trim($_POST['client_address']);
	if($action == "add"){
		$sql = "INSERT INTO `db_client` (`clientid`,`client_code`,`client_cname`,`client_ename`,`client_name`,`client_address`,`client_status`) VALUES (NULL,'$client_code','$client_cname','$client_ename','$client_name','$client_address',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:client.php");
		}
	}elseif($action == "edit"){
		$clientid = $_POST['clientid'];
		$client_status = $_POST['client_status'];
		$sql = "UPDATE `db_client` SET `client_code` = '$client_code',`client_cname` = '$client_cname',`client_ename` = '$client_ename',`client_name` = '$client_name',`client_address` = '$client_address',`client_status` = '$client_status' WHERE `clientid` = '$clientid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$clientid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_client` WHERE `clientid` IN ($clientid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>