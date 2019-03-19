<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	if($action == "add" || $action == "edit"){
		$applyer = $_POST['applyer'];
		$agenter = $_POST['agenter'];
		$apply_date = $_POST['apply_date'];
		$dotype = $_POST['dotype'];
		$vehicle_category = $_POST['vehicle_category'];
		$departure = trim($_POST['departure']);
		$destination = trim($_POST['destination']);
		$roundtype = $_POST['roundtype'];
		$start_time = $_POST['start_time'];
		$finish_time = $_POST['finish_time'];
		$other = trim($_POST['other']);
		$cause = trim($_POST['cause']);
		$dotime = fun_gettime();
		//查询申请人部门
		$sql_employee = "SELECT `deptid` FROM `db_employee` WHERE `employeeid` = '$applyer'";
		$result_employee = $db->query($sql_employee);
		if($result_employee->num_rows){
			$array_employee = $result_employee->fetch_assoc();
			$deptid = $array_employee['deptid'];
			//查询该部门第一个审批流程
			$sql_flow = "SELECT `flowid` FROM `db_vehicle_flow` WHERE `deptid` = '$deptid' ORDER BY `flow_order` ASC LIMIT 0,1";
			$result_flow = $db->query($sql_flow);
			if($action == "add"){
				//如果第一个流程flowid存在,执行插入申请用车记录.
				if($result_flow->num_rows){
					$array_flow = $result_flow->fetch_assoc();
					$flowid = $array_flow['flowid'];
					//自动生成编号
					$sql_vehicle_num = "SELECT MAX((SUBSTRING(`vehicle_num`,-4)+0)) AS `maxnum` FROM `db_vehicle_list` WHERE DATE_FORMAT(`apply_date`,'%Y') = YEAR(NOW())";
					$result_vehicle_num = $db->query($sql_vehicle_num);
					if($result_vehicle_num->num_rows){
						$array_vehicle_num = $result_vehicle_num->fetch_assoc();
						$maxnum = $array_vehicle_num['maxnum'];
						$nextnum = $maxnum + 1;
						$vehicle_num = date('Y') . strtolen($nextnum,4) . $nextnum;
					}else{
						$vehicle_num = date('Y')."0001";
					}
					$sql = "INSERT INTO `db_vehicle_list` (`listid`,`applyer`,`agenter`,`vehicle_num`,`deptid`,`apply_date`,`dotype`,`vehicle_category`,`departure`,`destination`,`roundtype`,`start_time`,`finish_time`,`other`,`cause`,`approve_status`,`vehicle_status`,`flowid`,`dotime`) VALUES (NULL,'$applyer','$agenter','$vehicle_num','$deptid','$apply_date','$dotype','$vehicle_category','$departure','$destination','$roundtype','$start_time','$finish_time','$other','$cause','A',1,'$flowid','$dotime')";
					$db->query($sql);
					if($listid = $db->insert_id){
						header("location:employee_vehicle_apply.php");
					}
				}else{
					die('下一审批节点错误，请与管理员联系！');
				}
				
			}elseif($action == "edit"){
				$listid = $_POST['listid'];
				if($result_flow->num_rows){
					$array_flow = $result_flow->fetch_assoc();
					$flowid = $array_flow['flowid'];
					$sql = "UPDATE `db_vehicle_list` SET `apply_date` = '$apply_date',`dotype` = '$dotype',`vehicle_category` = '$vehicle_category',`departure` = '$departure',`destination` = '$destination',`roundtype` = '$roundtype',`start_time` = '$start_time',`finish_time` = '$finish_time',`other` = '$other',`cause` = '$cause',`approve_status` = 'A',`flowid` = '$flowid' WHERE `listid` = '$listid'";
					$db->query($sql);
					if($db->affected_rows){
						header("location:employee_vehicle_apply.php");
					}
				}else{
					die('下一审批节点错误，请与管理员联系！');
				}
			}
		}
	}
	if($action == "del"){
		$array_listid = $_POST['id'];
		$listid = fun_convert_checkbox($array_listid);
		$sql_approve = "DELETE FROM `db_office_approve` WHERE `linkid` IN ($listid) AND `approve_type` = 'V'";
		$db->query($sql_approve);
		$sql = "DELETE FROM `db_vehicle_list` WHERE `listid` IN ($listid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
	if($action == "add" || $action == "edit"){
		$sql_vehicle = "SELECT `db_applyer`.`employee_name` AS `applyer_name`,`db_vehicle_list`.`vehicle_num`,`db_approver`.`email` FROM `db_vehicle_list` INNER JOIN `db_vehicle_flow` ON `db_vehicle_flow`.`flowid` = `db_vehicle_list`.`flowid` INNER JOIN `db_employee` AS `db_approver` ON `db_approver`.`employeeid` = `db_vehicle_flow`.`approver` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_vehicle_list`.`applyer` WHERE `db_vehicle_list`.`listid` = '$listid'";
		$result_vehicle = $db->query($sql_vehicle);
		if($result_vehicle->num_rows){
			$array_vehicle = $result_vehicle->fetch_assoc();
			$applyer_name = $array_vehicle['applyer_name'];
			$vehicle_num = $array_vehicle['vehicle_num'];
			$email_name = $array_vehicle['email'];
			$email_subject = "用车申请单".$vehicle_num."申请到达";
			$email_content = $applyer_name."申请的用车单".$vehicle_num."到达，请及时审批。";
			$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
			$db->query($sql_email);
		}
	}
}
?>