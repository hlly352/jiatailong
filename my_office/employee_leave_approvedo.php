<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$leaveid = $_POST['leaveid'];
	$approve_status = $_POST['approve_status'];
	$approve_content = trim($_POST['approve_content']);
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$approve_type = 'L';
	$dotime = fun_gettime();
	//查询审批信息
	$sql_leave = "SELECT `db_employee_leave`.`leavetime`,`db_employee_leave`.`approver`,`db_employee`.`position_type` FROM `db_employee_leave` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_leave`.`approver` WHERE `db_employee_leave`.`leaveid` = '$leaveid' AND `db_employee_leave`.`approver` = '$employeeid'";
	$result_leave = $db->query($sql_leave);
	if($result_leave->num_rows){
		$array_leave = $result_leave->fetch_assoc();
		$leavetime = $array_leave['leavetime'];
		$position_type = $array_leave['position_type'];
		$approver = $array_leave['approver'];
		if(($leavetime <= 24 && $position_type == 'B') || ($leavetime > 24 && $position_type == 'A') || $approve_status == 'C'){
			$sql_update = "UPDATE `db_employee_leave` SET `approve_status` = '$approve_status',`approver` = 0 WHERE `leaveid` = '$leaveid'";
		}else{
			//查询审批人的上级领导
			$sql_superior = "SELECT `superior` FROM `db_employee` WHERE `employeeid` = '$approver' AND `superior` != 0";
			$result_superior = $db->query($sql_superior);
			if($result_superior->num_rows){
				$array_superior = $result_superior->fetch_assoc();
				$next_approver = $array_superior['superior'];
				$sql_update = "UPDATE `db_employee_leave` SET `approver` = '$next_approver' WHERE `leaveid` = '$leaveid'";
			}else{
				$sql_update = "UPDATE `db_employee_leave` SET `approve_status` = '$approve_status',`approver` = 0 WHERE `leaveid` = '$leaveid'";
			}
		}
		$db->query($sql_update);
		$sql = "INSERT INTO `db_office_approve` (`approveid`,`approve_content`,`approve_status`,`certigier`,`approver`,`dotime`,`approve_type`,`linkid`) VALUES (NULL,'$approve_content','$approve_status','$employeeid','$employeeid','$dotime','$approve_type','$leaveid')";
		$db->query($sql);
		if($db->insert_id){
			$sql_leave = "SELECT `db_applyer`.`employee_name` AS `applyer_name`,`db_agenter`.`email` AS `agenter_email`,`db_approver`.`email` AS `approver_email`,`db_employee_leave`.`leave_num`,`db_employee_leave`.`approver`,`db_employee_leave`.`approve_status` FROM `db_employee_leave` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_leave`.`applyer` INNER JOIN `db_employee` AS `db_agenter` ON `db_agenter`.`employeeid` = `db_employee_leave`.`agenter` LEFT JOIN `db_employee` AS `db_approver` ON `db_approver`.`employeeid` = `db_employee_leave`.`approver` WHERE `db_employee_leave`.`leaveid` = '$leaveid'";
			$result_leave = $db->query($sql_leave);
			if($result_leave->num_rows){
				$array_leave = $result_leave->fetch_assoc();
				$leave_num = $array_leave['leave_num'];
				$applyer_name = $array_leave['applyer_name'];
				$wait_approver = $array_leave['approver'];
				$approve_status = $array_leave['approve_status'];
				$approve_result = $array_office_approve_status[$approve_status];
				if(($approve_status == 'B' || $approve_status == 'C') && $wait_approver == 0){
					$email_name = $array_leave['agenter_email'];
					$email_subject = "请假单".$leave_num."审批结果";
					$email_content = "您申请的".$applyer_name."请假单".$leave_num."审批完成，审批结果为".$approve_result."。";
				}elseif($approve_status == 'A' && $wait_approver != 0){
					$email_name = $array_leave['approver_email'];
					$email_subject = "请假单".$leave_num."申请到达";
					$email_content = $applyer_name."申请的请假单".$leave_num."到达，请及时审批。";
				}
				$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
				$db->query($sql_email);
			}
		}
		header("location:employee_leave_approve_list.php");
	}
}
?>