<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$team_name = trim($_POST['team_name']);
	if($action == "add"){
		$sql = "INSERT INTO `db_responsibility_team` (`teamid`,`team_name`,`team_status`) VALUES (NULL,'$team_name',1)";
		$db->query($sql);
		if($db->insert_id){
			header("location:mould_responsibility_team.php");
		}
	}elseif($action == "edit"){
		$teamid = $_POST['teamid'];
		$team_status = $_POST['team_status'];
		$sql = "UPDATE `db_responsibility_team` SET `team_name` = '$team_name',`team_status` = '$team_status'  WHERE `teamid` = '$teamid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$teamid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_responsibility_team` WHERE `teamid` IN ($teamid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>