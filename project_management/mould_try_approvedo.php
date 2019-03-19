<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$tryid = $_POST['tryid'];
	$approve_status = $_POST['approve_status'];
	$approve_content = trim($_POST['approve_content']);
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$approve_type = 'MT';
	$dotime = fun_gettime();
	$sql = "INSERT INTO `db_office_approve` (`approveid`,`approve_content`,`approve_status`,`certigier`,`approver`,`dotime`,`approve_type`,`linkid`) VALUES (NULL,'$approve_content','$approve_status','$employeeid','$employeeid','$dotime','$approve_type','$tryid')";
	$db->query($sql);
	if($db->insert_id){
		$sql_update = "UPDATE `db_mould_try` SET `approve_status` = '$approve_status' WHERE `tryid` = '$tryid'";
		$db->query($sql_update);
		if($db->affected_rows){
			$sql_mould_try = "SELECT `db_mould`.`mould_number`,`db_employee`.`employee_name`,`db_employee`.`email` FROM `db_mould_try` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_try`.`employeeid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` WHERE `db_mould_try`.`tryid` = '$tryid'";
			$result_mould_try = $db->query($sql_mould_try);
			if($result_mould_try->num_rows){
				$array_mould_try = $result_mould_try->fetch_assoc();
				$mould_number = $array_mould_try['mould_number'];
				$employee_name = $array_mould_try['employee_name'];
				$email_name = $array_mould_try['email'];
				$approve_result = $array_office_approve_status[$approve_status];
				$email_subject = $mould_number."试模申请审批结果";
				$email_content = "您申请的".$mould_number."试模审批完成，审批结果为".$approve_result."。";
				$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
				$db->query($sql_email);
			}
			header("location:mould_try_approve_list.php");
		}
	}
}
?>