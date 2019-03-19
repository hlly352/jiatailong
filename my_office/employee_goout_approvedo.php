<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$gooutid = $_POST['gooutid'];
	$approve_status = $_POST['approve_status'];
	$approve_content = trim($_POST['approve_content']);
	$approve_type = 'G';
	$dotime = fun_gettime();
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$sql = "INSERT INTO `db_office_approve` (`approveid`,`approve_content`,`approve_status`,`certigier`,`approver`,`approve_type`,`linkid`,`dotime`) VALUES (NULL,'$approve_content','$approve_status','$employeeid','$employeeid','$approve_type','$gooutid','$dotime')";
	$db->query($sql);
	if($db->insert_id){
		$sql_update = "UPDATE `db_employee_goout` SET `approve_status` = '$approve_status' WHERE `gooutid` = '$gooutid'";
		$db->query($sql_update);
		if($db->affected_rows){
			$sql_goout = "SELECT `db_applyer`.`employee_name` AS `applyer_name`,`db_agenter`.`email`,`db_employee_goout`.`goout_num` FROM `db_employee_goout` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_goout`.`applyer` INNER JOIN `db_employee` AS `db_agenter` ON `db_agenter`.`employeeid` = `db_employee_goout`.`agenter` WHERE `db_employee_goout`.`gooutid` = '$gooutid'";
			$result_goout = $db->query($sql_goout);
			if($result_goout->num_rows){
				$array_goout = $result_goout->fetch_assoc();
				$goout_num = $array_goout['goout_num'];
				$applyer_name = $array_goout['applyer_name'];
				$email_name = $array_goout['email'];
				$approve_result = $array_office_approve_status[$approve_status];
				$email_subject = "出门证".$goout_num."审批结果";
				$email_content = "您申请的".$applyer_name."出门证".$goout_num."审批完成，审批结果为".$approve_result."。";
				$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
				$db->query($sql_email);
			}
			header("location:employee_goout_approve_list.php");
		}
	}
}
?>