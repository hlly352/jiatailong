<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$apply_date = $_POST['apply_date'];
	$vacationid = $_POST['vacationid'];
	$work_shift = $_POST['work_shift'];
	$start_time = $_POST['start_time'];
	$finish_time = $_POST['finish_time'];
	$leavetime = $_POST['leavetime'];
	$leave_cause = trim($_POST['leave_cause']);
	$dotime = fun_gettime();
	if($action == "add" || $action == "edit"){
		$applyer = $_POST['applyer'];
		//查询申请人的上级领导
		$sql_superior = "SELECT `superior` FROM `db_employee` WHERE `employeeid` = '$applyer'";
		$result_superior = $db->query($sql_superior);
		if($result_superior->num_rows){
			$array_superior = $result_superior->fetch_assoc();
			$approver = $array_superior['superior']?$array_superior['superior']:$applyer;
		}
	}
	if($action == "add"){
		$applyer = $_POST['applyer'];
		$agenter = $_POST['agenter'];
		//自动生成编号
		$sql_num = "SELECT MAX((SUBSTRING(`leave_num`,-4)+0)) AS `maxnum` FROM `db_employee_leave` WHERE DATE_FORMAT(`apply_date`,'%Y') = YEAR(NOW())";
		$result_num = $db->query($sql_num);
		if($result_num->num_rows){
			$array_num = $result_num->fetch_assoc();
			$maxnum = $array_num['maxnum'];
			$nextnum = $maxnum+1;
			$leave_num = date('Y') . strtolen($nextnum,4) . $nextnum;
		}else{
			$leave_num = date('Y')."0001";
		}
		$sql = "INSERT INTO `db_employee_leave` (`leaveid`,`leave_num`,`applyer`,`agenter`,`apply_date`,`vacationid`,`work_shift`,`start_time`,`finish_time`,`leavetime`,`leavetime_valid`,`leave_cause`,`approve_status`,`leave_status`,`approver`,`dotime`) VALUES (NULL,'$leave_num','$applyer','$agenter','$apply_date','$vacationid','$work_shift','$start_time','$finish_time','$leavetime','$leavetime','$leave_cause','A',1,'$approver','$dotime')";
		$db->query($sql);
		if($leaveid = $db->insert_id){
			header("location:employee_leave_apply.php");
		}
	}elseif($action == "edit"){
		$leaveid = $_POST['leaveid'];
		$sql = "UPDATE `db_employee_leave` SET `apply_date` = '$apply_date',`vacationid` = '$vacationid',`work_shift` = '$work_shift',`start_time` = '$start_time',`finish_time` = '$finish_time',`leavetime` = '$leavetime',`leavetime_valid` = '$leavetime',`leave_cause` = '$leave_cause',`approve_status` = 'A',`approver` = '$approver' WHERE `leaveid` = '$leaveid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:employee_leave_apply.php");
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$leaveid = fun_convert_checkbox($array_id);
		$sql_approve = "DELETE FROM `db_office_approve` WHERE `linkid` IN ($leaveid) AND `approvetype` = 'L'";
		$db->query($sql_approve);
		$sql = "DELETE FROM `db_employee_leave` WHERE `leaveid` IN ($leaveid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
	if($action == "add" || $action == "edit"){
		$sql_leave = "SELECT `db_applyer`.`employee_name` AS `applyer_name`,`db_approver`.`email`,`db_employee_leave`.`leave_num` FROM `db_employee_leave` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_leave`.`applyer` INNER JOIN `db_employee` AS `db_approver` ON `db_approver`.`employeeid` = `db_employee_leave`.`approver` WHERE `db_employee_leave`.`leaveid` = '$leaveid'";
		$result_leave = $db->query($sql_leave);
		if($result_leave->num_rows){
			$array_employee = $result_leave->fetch_assoc();
			$leave_num = $array_employee['leave_num'];
			$applyer_name = $array_employee['applyer_name'];
			$email_name = $array_employee['email'];
			$email_subject = "请假单".$leave_num."申请到达";
			$email_content = $applyer_name."申请的请假单".$leave_num."到达，请及时审批。";
			$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
			$db->query($sql_email);
		}
	}
}
?>