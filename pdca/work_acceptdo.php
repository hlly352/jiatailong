<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$deadline_date = $_POST['deadline_date'];
	$workid = $_POST['workid'];
	$sql = "UPDATE `db_work` SET `deadline_date` = '$deadline_date',`pdca_status` = 'D' WHERE `workid` = '$workid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>