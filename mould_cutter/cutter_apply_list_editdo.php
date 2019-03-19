<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$apply_listid = $_POST['apply_listid'];
	$quantity = $_POST['quantity'];
	$mouldid = $_POST['mouldid'];
	$plan_date = $_POST['plan_date'];
	$remark = trim($_POST['remark']);
	$sql = "UPDATE `db_cutter_apply_list` SET `quantity` = '$quantity',`mouldid` = '$mouldid',`plan_date` = '$plan_date',`remark` = '$remark' WHERE `apply_listid` = '$apply_listid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>