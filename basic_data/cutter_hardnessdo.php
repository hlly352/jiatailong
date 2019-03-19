<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$texture = $_POST['texture'];
	$hardness = trim($_POST['hardness']);
	if($action == "add"){
		$sql = "INSERT INTO `db_cutter_hardness` (`hardnessid`,`hardness`,`texture`) VALUES (NULL,'$hardness','$texture')";
		$db->query($sql);
		if($db->insert_id){
			header("location:cutter_hardness.php");
		}
	}elseif($action == "edit"){
		$hardnessid = $_POST['hardnessid'];
		$sql = "UPDATE `db_cutter_hardness` SET `texture` = '$texture',`hardness` = '$hardness' WHERE `hardnessid` = '$hardnessid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}
	}elseif($action == "del"){
		$array_hardnessid = fun_convert_checkbox($_POST['id']);
		$sql = "DELETE FROM `db_cutter_hardness` WHERE `hardnessid` IN ($array_hardnessid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>