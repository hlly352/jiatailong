<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$inc_cname = trim($_POST['inc_cname']);
	$inc_ename = trim($_POST['inc_ename']);
	$inc_contact = trim($_POST['inc_contact']);
	$inc_phone = trim($_POST['inc_phone']);
	if($action == "add"){
		$sql = "INSERT INTO `db_express_inc` (`incid`,`inc_cname`,`inc_ename`,`inc_contact`,`inc_phone`,`inc_status`) VALUES (NULL,'$inc_cname','$inc_ename','$inc_contact','$inc_phone',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:express_inc.php");
		}
	}elseif($action == "edit"){
		$incid = $_POST['incid'];
		$inc_status = $_POST['inc_status'];
		$sql = "UPDATE `db_express_inc` SET `inc_cname` = '$inc_cname',`inc_ename` = '$inc_ename',`inc_contact` = '$inc_contact',`inc_phone` = '$inc_phone',`inc_status` = '$inc_status' WHERE `incid` = '$incid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$incid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_express_inc` WHERE `incid` IN ($incid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}	 
?>