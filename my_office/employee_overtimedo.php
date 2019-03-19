<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$apply_date = $_POST['apply_date'];
	$overtime_date = $_POST['overtime_date'];
	$start_time = $overtime_date.' '.$_POST['start_time'];
	$finish_time = $overtime_date.' '.$_POST['finish_time'];
	$overtime = $_POST['overtime'];
	$overtime_cause = trim($_POST['overtime_cause']);
	$dotime = fun_gettime();
	if($action == "add"){
		$agenter = $_POST['agenter'];
		$applyer = $_POST['applyer'];
		$employeeid = $_POST['applyer'];
		//自动生成编号
		$sql_num = "SELECT MAX((SUBSTRING(`overtime_num`,-4)+0)) AS `maxnum` FROM `db_employee_overtime` WHERE DATE_FORMAT(`apply_date`,'%Y') = YEAR(NOW())";
		$result_num = $db->query($sql_num);
		if($result_num->num_rows){
			$array_num = $result_num->fetch_assoc();
			$maxnum = $array_num['maxnum'];
			$nextnum = $maxnum+1;
			$overtime_num = date('Y').strtolen($nextnum,4).$nextnum;
		}else{
			$overtime_num = date('Y')."0001";
		}
		//查询审批人
		$sql_employee = "SELECT `position_type` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
		$result_employee = $db->query($sql_employee);
		if($result_employee->num_rows){
			$array_employee = $result_employee->fetch_assoc();
			$position_type = $array_employee['position_type'];
			while($position_type != 'B' && $position_type != 'A'){
				$sql_superior ="SELECT `db_superior`.`position_type`,`db_employee`.`superior` FROM `db_employee` LEFT JOIN `db_employee` AS `db_superior` ON `db_superior`.`employeeid` = `db_employee`.`superior` WHERE `db_employee`.`employeeid` = '$employeeid'";
				$result_superior = $db->query($sql_superior);
				if($result_superior->num_rows){
					$array_superior = $result_superior->fetch_assoc();
					$position_type = $array_superior['position_type'];
				}
				$employeeid = $array_superior['superior'];
			}
			$approver = $array_superior['superior']?$array_superior['superior']:$employeeid;
		}
		$sql = "INSERT INTO `db_employee_overtime` (`overtimeid`,`overtime_num`,`applyer`,`agenter`,`apply_date`,`start_time`,`finish_time`,`overtime`,`overtime_valid`,`overtime_cause`,`approve_status`,`overtime_status`,`approver`,`dotime`) VALUES (NULL,'$overtime_num','$applyer','$agenter','$apply_date','$start_time','$finish_time','$overtime','$overtime','$overtime_cause','A',1,'$approver','$dotime')";
		$db->query($sql);
		if($overtimeid = $db->insert_id){
			header("location:employee_overtime_apply.php");
		}
	}elseif($action == "edit"){
		$overtimeid = $_POST['overtimeid'];
		$sql = "UPDATE `db_employee_overtime` SET `apply_date` = '$apply_date',`start_time` = '$start_time',`finish_time` = '$finish_time',`overtime` = '$overtime',`overtime_valid` = '$overtime',`overtime_cause` = '$overtime_cause',`approve_status` = 'A' WHERE `overtimeid` = '$overtimeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:employee_overtime_apply.php");
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$overtimeid = fun_convert_checkbox($array_id);
		$sql_approve = "DELETE FROM `db_office_approve` WHERE `linkid` IN ($overtimeid) AND `approve_type` = 'O'";
		$db->query($sql_approve);
		$sql = "DELETE FROM `db_employee_overtime` WHERE `overtimeid` IN ($overtimeid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
	if($action == "add" || $action == "edit"){
		$sql_overtime = "SELECT `db_applyer`.`employee_name` AS `applyer_name`,`db_approver`.`email`,`db_employee_overtime`.`overtime_num` FROM `db_employee_overtime` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_overtime`.`applyer` INNER JOIN `db_employee` AS `db_approver` ON `db_approver`.`employeeid` = `db_employee_overtime`.`approver` WHERE `db_employee_overtime`.`overtimeid` = '$overtimeid'";
		$result_overtime = $db->query($sql_overtime);
		if($result_overtime->num_rows){
			$array_overtimet = $result_overtime->fetch_assoc();
			$overtime_num = $array_overtimet['overtime_num'];
			$applyer_name = $array_overtimet['applyer_name'];
			$email_name = $array_overtimet['email'];
			$email_subject = "加班单".$overtime_num."申请到达";
			$email_content = $applyer_name."申请的加班单".$overtime_num."到达，请及时审批。";
			$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
			$db->query($sql_email);
		}
	}
}
?>