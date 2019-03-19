<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$brand = trim($_POST['brand']);
	if($action == "add"){
		$sql = "INSERT INTO `db_cutter_brand` (`brandid`,`brand`) VALUES (NULL,'$brand')";
		$db->query($sql);
		if($db->insert_id){
			header("location:cutter_brand.php");
		}
	}elseif($action == "edit"){
		$brandid = $_POST['brandid'];
		$sql = "UPDATE `db_cutter_brand` SET `brand` = '$brand' WHERE `brandid` = '$brandid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}
	}elseif($action == "del"){
		$array_brandid = fun_convert_checkbox($_POST['id']);
		$sql = "DELETE FROM `db_cutter_brand` WHERE `brandid` IN ($array_brandid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>