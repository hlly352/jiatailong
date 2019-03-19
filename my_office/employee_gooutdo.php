<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$apply_date = $_POST['apply_date'];
	$destination = trim($_POST['destination']);
	$start_time = $_POST['start_time'];
	$finish_time = $_POST['finish_time'];
	$goout_cause = trim($_POST['goout_cause']);
	$dotime = fun_gettime();
	if($action == "add"){
		$applyer = $_POST['applyer'];
		$agenter = $_POST['agenter'];
		//自动生成编号
		$sql_num = "SELECT MAX((SUBSTRING(`goout_num`,-4)+0)) AS `maxnum` FROM `db_employee_goout` WHERE DATE_FORMAT(`apply_date`,'%Y') = YEAR(NOW())";
		$result_num = $db->query($sql_num);
		if($result_num->num_rows){
			$array_num = $result_num->fetch_assoc();
			$maxnum = $array_num['maxnum'];
			$nextnum = $maxnum+1;
			$goout_num = date('Y').strtolen($nextnum,4).$nextnum;
		}else{
			$goout_num = date('Y')."0001";
		}
		$sql = "INSERT INTO `db_employee_goout` (`gooutid`,`goout_num`,`applyer`,`agenter`,`apply_date`,`destination`,`start_time`,`finish_time`,`goout_cause`,`approve_status`,`goout_status`,`dotime`) VALUES (NULL,'$goout_num','$applyer','$agenter','$apply_date','$destination','$start_time','$finish_time','$goout_cause','A',1,'$dotime')";
		$db->query($sql);
		if($gooutid = $db->insert_id){
			header("location:employee_goout_apply.php");
		}
	}elseif($action == "edit"){
		$gooutid = $_POST['gooutid'];
		$sql = "UPDATE `db_employee_goout` SET `apply_date` = '$apply_date',`destination` = '$destination',`start_time` = '$start_time',`finish_time` = '$finish_time',`goout_cause` = '$goout_cause',`approve_status` = 'A' WHERE `gooutid` = '$gooutid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:employee_goout_apply.php");
		}
	}elseif($action == "del"){
		$array_gooutid = $_POST['id'];
		$gooutid = fun_convert_checkbox($array_gooutid);
		$sql_approve = "DELETE FROM `db_office_approve` WHERE `linkid` IN ($gooutid) AND `approve_type` = 'G'";
		$db->query($sql_approve);
		$sql = "DELETE FROM `db_employee_goout` WHERE `gooutid` IN ($gooutid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
	if($action == "add" || $action == "edit"){
		$sql_goout = "SELECT `db_applyer`.`employee_name` AS `applyer_name`,IF(`db_applyer`.`position_type` IN ('A','B'),`db_applyer`.`email`,`db_superior`.`email`) AS `email`,`db_employee_goout`.`goout_num` FROM `db_employee_goout` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_goout`.`applyer` LEFT JOIN `db_employee` AS `db_superior` ON `db_superior`.`employeeid` = `db_applyer`.`superior` WHERE `db_employee_goout`.`gooutid` = '$gooutid'";
		$result_goout = $db->query($sql_goout);
		if($result_goout->num_rows){
			$array_goout = $result_goout->fetch_assoc();
			$goout_num = $array_goout['goout_num'];
			$applyer_name = $array_goout['applyer_name'];
			$email_name = $array_goout['email'];
			$email_subject = "出门证".$goout_num."申请到达";
			$email_content = $applyer_name."申请的出门证".$goout_num."到达，请及时审批。";
			$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
			$db->query($sql_email);
		}
	}
}
?>