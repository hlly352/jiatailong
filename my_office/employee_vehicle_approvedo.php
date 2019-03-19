<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$listid = $_POST['listid'];
	$approve_status = $_POST['approve_status'];
	$approve_content = trim($_POST['approve_content']);
	$approve_type = 'V';
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$certigier = $_POST['certigier'];
	$dotime = fun_gettime();
	//插入审批记录
	$sql = "INSERT INTO `db_office_approve` (`approveid`,`approve_content`,`approve_status`,`certigier`,`approver`,`approve_type`,`linkid`,`dotime`) VALUES (NULL,'$approve_content','$approve_status','$certigier','$employeeid','$approve_type','$listid','$dotime')";
	$db->query($sql);
	if($db->insert_id){
		if($approve_status == 'B'){
			//如果插入成功,查询该申请记录的部门,流程级别.
			$sql_vehivle_list = "SELECT `db_vehicle_list`.`deptid`,`db_vehicle_flow`.`flow_order`,`db_vehicle_flow`.`iscontrol` FROM `db_vehicle_list` INNER JOIN `db_vehicle_flow` ON `db_vehicle_flow`.`flowid` = `db_vehicle_list`.`flowid` WHERE `db_vehicle_list`.`listid` = '$listid'";
			$result_vehicle_list = $db->query($sql_vehivle_list);
			if($result_vehicle_list->num_rows){
				$array_vehiecle_list = $result_vehicle_list->fetch_assoc();
				$deptid = $array_vehiecle_list['deptid'];
				$flow_order = $array_vehiecle_list['flow_order'];
				$iscontrol = $array_vehiecle_list['iscontrol'];
				//查询该流程的下一级别flowid
				$sql_next_flow = "SELECT `db_vehicle_flow`.`flowid` FROM `db_vehicle_flow` WHERE `db_vehicle_flow`.`deptid` = '$deptid' AND `db_vehicle_flow`.`flow_order` > '$flow_order' ORDER BY `db_vehicle_flow`.`flow_order` ASC LIMIT 0,1";
				$result_next_flow = $db->query($sql_next_flow);
				if($result_next_flow->num_rows){
					$array_next_flow = $result_next_flow->fetch_assoc();
					$flowid = $array_next_flow['flowid'];
					$approve_status = 'A';
				}else{
					$flowid = 0;
					$approve_status = 'B';
				}
				if($iscontrol){
					$pathtype = $_POST['pathtype'];
					$vehicleid = $_POST['vehicleid'];
					$sql_vehicle = "SELECT IF('$pathtype' = 'A',`charge_in`,`charge_out`) AS `charge`,`charge_wait` FROM `db_vehicle` WHERE `vehicleid` = '$vehicleid'";
					$result_vehicle = $db->query($sql_vehicle);
					if($result_vehicle->num_rows){
						$array_vehicle = $result_vehicle->fetch_assoc();
						$charge = $array_vehicle['charge'];
						$charge_wait = $array_vehicle['charge_wait'];
						$sql_veihcle_update = ",`pathtype` = '$pathtype',`vehicleid` = '$vehicleid',`charge` = '$charge',`charge_wait` = '$charge_wait'";
					}
				}
				//更新申请记录的下一级审批flowid,审批状态
				$sql_update_vehicel_list = "UPDATE `db_vehicle_list` SET `flowid` = '$flowid',`approve_status` = '$approve_status' $sql_veihcle_update WHERE `listid` = '$listid'";
				$db->query($sql_update_vehicel_list);
				if($db->affected_rows){
					header("location:employee_vehicle_approve_list.php");
				}
			}
		}elseif($approve_status == 'C'){
			$sql_update_vehicel_list = "UPDATE `db_vehicle_list` SET `approve_status` = '$approve_status',`flowid` = 0 WHERE `listid` = '$listid'";
			$db->query($sql_update_vehicel_list);
			if($db->affected_rows){
				header("location:employee_vehicle_approve_list.php");
			}
		}
	}

	$sql_vehicle = "SELECT `db_applyer`.`employee_name` AS `applyer_name`,`db_agenter`.`email` AS `agenter_email`,`db_approver`.`email` AS `approver_email`,`db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`approve_status`,`db_vehicle_list`.`flowid` FROM `db_vehicle_list` LEFT JOIN `db_vehicle_flow` ON `db_vehicle_flow`.`flowid` = `db_vehicle_list`.`flowid` LEFT JOIN `db_employee` AS `db_approver` ON `db_approver`.`employeeid` = `db_vehicle_flow`.`approver` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_vehicle_list`.`applyer` INNER JOIN `db_employee` AS `db_agenter` ON `db_agenter`.`employeeid` = `db_vehicle_list`.`agenter` WHERE `db_vehicle_list`.`listid` = '$listid'";
	$result_vehicle = $db->query($sql_vehicle);
	if($result_vehicle->num_rows){
		$array_vehicle = $result_vehicle->fetch_assoc();
		$flowid = $array_vehicle['flowid'];
		$applyer_name = $array_vehicle['applyer_name'];
		$vehicle_num = $array_vehicle['vehicle_num'];
		$approve_status = $array_vehicle['approve_status'];
		if($approve_status == 'A' && $flowid != 0){
			$email_name = $array_vehicle['approver_email'];
			$email_subject = "用车单".$vehicle_num."申请到达";
			$email_content = $applyer_name."申请的用车单".$vehicle_num."到达，请及时审批。";
		}elseif($approve_status == 'B' || $approve_status == 'C'){
			$approve_result = $array_office_approve_status[$approve_status];
			$email_name = $array_vehicle['agenter_email'];
			$email_subject = "用车申请".$vehicle_num."审批结果";
			$email_content = "您申请的".$applyer_name."用车单".$vehicle_num."审批完成,审批结果为".$approve_result."。";
		}
		$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
		$db->query($sql_email);
	}
}
?>