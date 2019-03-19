<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$plan_content = htmlcode($_POST['plan_content']);
	$end_date = $_POST['end_date'];
	$employee = trim($_POST['employee']);
	$workid = $_POST['workid'];
	if($action == "add"){
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
		$sql = "INSERT INTO `db_work_plan` (`planid`,`plan_content`,`end_date`,`employee`,`workid`,`employeeid`,`dotime`) VALUES (NULL,'$plan_content','$end_date','$employee','$workid','$employeeid','$dotime')";
		$db->query($sql);
		if($db->insert_id){
			header("location:work_update.php?id=".$workid);
		}
	}elseif($action == "edit"){
		$planid = $_POST['planid'];
		$sql = "UPDATE `db_work_plan` SET `plan_content` = '$plan_content',`end_date` = '$end_date',`employee` = '$employee' WHERE `planid` = '$planid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:work_update.php?id=".$workid);
		}
	}elseif($action == "del"){
		$array_planid = $_POST['id'];
		$planid = fun_convert_checkbox($array_planid);
		$sql = "DELETE FROM `db_work_plan` WHERE `planid` IN ($planid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>