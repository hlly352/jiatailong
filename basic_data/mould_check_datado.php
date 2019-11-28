<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$page = $_POST['page'];
$type = $_POST['type'];
if($_POST['submit']){
	$action = $_POST['action'];
	$typeid = trim($_POST['typeid']);
	$degree = $_POST['degree'];
	$checkname = htmlspecialchars(trim($_POST['check_name']));
	if($action == "add"){	
		//添加新的项目名称
		$sql = "INSERT INTO `db_mould_check_data`(`categoryid`,`degree`,`checkname`) VALUES('$typeid','$degree','$checkname')";
		$db->query($sql);
		header("location:mould_check_data.php");
	}elseif($action == "edit"){
		$checkid = $_POST['id'];
		$sql = "UPDATE `db_mould_check_data` SET `degree` = '$degree',`categoryid` = '$typeid',`checkname` = '$checkname' WHERE `id` = '$checkid'";
		$db->query($sql);
		header("location:mould_check_data.php?submit=查询&type=".$type."&page=".$page);
		
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$checkid = fun_convert_checkbox($array_id);
		///////////////
		//检测是否有已测评项目//
		///////////////
		$sql = "DELETE FROM `db_mould_check_data` WHERE `id` IN ($checkid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>