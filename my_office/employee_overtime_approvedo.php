<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$overtimeid = $_POST['overtimeid'];
	$start_time = $_POST['start_time'];
	$finish_time = $_POST['finish_time'];
	$overtime = $_POST['overtime'];
	$approve_status = $_POST['approve_status'];
	$approve_content = trim($_POST['approve_content']);
	$approve_type = 'O';
	$dotime = fun_gettime();
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$sql = "INSERT INTO `db_office_approve` (`approveid`,`approve_content`,`approve_status`,`certigier`,`approver`,`approve_type`,`linkid`,`dotime`) VALUES (NULL,'$approve_content','$approve_status','$employeeid','$employeeid','$approve_type','$overtimeid','$dotime')";
	$db->query($sql);
	if($db->insert_id){
		$sql_update = "UPDATE `db_employee_overtime` SET `start_time` = '$start_time',`finish_time` = '$finish_time',`overtime` = '$overtime',`approve_status` = '$approve_status' WHERE `overtimeid` = '$overtimeid'";
		$db->query($sql_update);
		if($db->affected_rows){
			$sql_overtime = "SELECT `db_applyer`.`employee_name` AS `applyer_name`,`db_agenter`.`email`,`db_employee_overtime`.`overtime_num` FROM `db_employee_overtime` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_overtime`.`applyer` INNER JOIN `db_employee` AS `db_agenter` ON `db_agenter`.`employeeid` = `db_employee_overtime`.`agenter` WHERE `db_employee_overtime`.`overtimeid` = '$overtimeid'";
			$result_overtime = $db->query($sql_overtime);
			if($result_overtime->num_rows){
				$array_overtime = $result_overtime->fetch_assoc();
				$overtime_num = $array_overtime['overtime_num'];
				$applyer_name = $array_overtime['applyer_name'];
				$email_name = $array_overtime['email'];
				$approve_result = $array_office_approve_status[$approve_status];
				$email_subject = "加班单".$overtime_num."审批结果";
				$email_content = "您申请的".$applyer_name."加班单".$overtime_num."审批完成,审批结果为".$approve_result."。";
				$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
				$db->query($sql_email);
			}
			header("location:employee_overtime_approve_list.php");
		}
	}
}
?>