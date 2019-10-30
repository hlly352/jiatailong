<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$apply_date = $_POST['apply_date'];
	$meetingroom = $_POST['meetingroom'];
	$start_time = $_POST['start_time'];
	$end_time = $_POST['end_time'];
	$meeting_subject = trim($_POST['meeting_subject']);
	$meeting_remark = trim($_POST['meeting_remark']);
	$dotime = fun_gettime();
	if($action == "add"){
		$applyer = $_POST['applyer'];
		$sql = "INSERT INTO `db_employee_meeting` (`applyer`,`apply_date`,`meetingroom`,`start_time`,`end_time`,`meeting_subject`,`meeting_remark`,`meeting_status`,`dotime`) VALUES ('$applyer','$apply_date','$meetingroom','$start_time','$end_time','$meeting_subject','$meeting_remark',1,'$dotime')";
		$db->query($sql);
		if($meetingid = $db->insert_id){
			header("location:employee_meeting_apply.php");
		}
	}elseif($action == "edit"){
		$meetingid = $_POST['meetingid'];
		$sql = "UPDATE `db_employee_meeting` SET `apply_date` = '$apply_date',`meetingroom` = '$meetingroom',`start_time` = '$start_time',`end_time` = '$end_time',`meeting_subject` = '$meeting_subject',`meeting_remark` = '$meeting_remark' WHERE `meetingid` = '$meetingid'";
		echo $sql;
		$db->query($sql);
		if($db->affected_rows){
			header("location:employee_meeting_apply.php");
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$expressid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_employee_express` WHERE `expressid` IN ($expressid)";
		$db->query($sql);
		$sql_approve = "DELETE FROM `db_office_approve` WHERE `linkid` IN ($expressid) AND `approve_type` = 'E'";
		$db->query($sql_approve);
		header("location:".$_SERVER['HTTP_REFERER']);
	}
	if($action == "add" || $action == "edit"){
		$sql_express = "SELECT `db_applyer`.`employee_name` AS `applyer_name`,IF(`db_applyer`.`position_type` IN ('A','B'),`db_applyer`.`email`,`db_superior`.`email`) AS `email`,`db_employee_express`.`express_num`,`db_express_inc`.`inc_cname` FROM `db_employee_express` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express`.`express_incid` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_express`.`applyer` LEFT JOIN `db_employee` AS `db_superior` ON `db_superior`.`employeeid` = `db_applyer`.`superior` WHERE `db_employee_express`.`expressid` = '$expressid'";
		$result_express = $db->query($sql_express);
		if($result_express->num_rows){
			$array_express = $result_express->fetch_assoc();
			$express_num = $array_express['express_num'];
			$express_inc_cname = $array_express['inc_cname'];
			$applyer_name = $array_express['applyer_name'];
			$email_name = $array_express['email'];
			$email_subject = "快递".$express_num."申请到达";
			$email_content = $applyer_name."申请的快递".$express_inc_cname.$express_num."到达，请及时审批。";
			$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
			$db->query($sql_email);
		}
	}
}
?>