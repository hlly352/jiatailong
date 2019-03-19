<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$expressid = $_POST['expressid'];
	$approve_status = $_POST['approve_status'];
	$approve_content = trim($_POST['approve_content']);
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$approve_type = 'E';
	$dotime = fun_gettime();
	$sql = "INSERT INTO `db_office_approve` (`approveid`,`approve_content`,`approve_status`,`certigier`,`approver`,`dotime`,`approve_type`,`linkid`) VALUES (NULL,'$approve_content','$approve_status','$employeeid','$employeeid','$dotime','$approve_type','$expressid')";
	$db->query($sql);
	if($db->insert_id){
		$sql_update = "UPDATE `db_employee_express` SET `approve_status` = '$approve_status' WHERE `expressid` = '$expressid'";
		$db->query($sql_update);
		if($db->affected_rows){
			$sql_express = "SELECT `db_applyer`.`employee_name` AS `applyer_name`,`db_agenter`.`email`,`db_employee_express`.`express_num`,`db_express_inc`.`inc_cname` FROM `db_employee_express` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express`.`express_incid` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_express`.`applyer` INNER JOIN `db_employee` AS `db_agenter` ON `db_agenter`.`employeeid` = `db_employee_express`.`agenter` WHERE `db_employee_express`.`expressid` = '$expressid'";
			$result_express = $db->query($sql_express);
			if($result_express->num_rows){
				$array_express = $result_express->fetch_assoc();
				$express_num = $array_express['express_num'];
				$express_inc_cname = $array_express['inc_cname'];
				$applyer_name = $array_express['applyer_name'];
				$email_name = $array_express['email'];
				$approve_result = $array_office_approve_status[$approve_status];
				$email_subject = "快递申请".$express_num."审批结果";
				$email_content = "您申请的".$applyer_name.'快递'.$express_inc_cname.$express_num."审批完成，审批结果为".$approve_result."。";
				$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
				$db->query($sql_email);
			}
			header("location:employee_express_approve_list.php");
		}
	}
}
?>