<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
	$action = $_REQUEST['action'];
	$planid = $_GET['planid'];

	if($action == 'approval'){
		$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '6' WHERE `planid` = '$planid'";
		$db->query($sql);
		if($db->affected_rows){
			header('location:material_funds_plan.php');
		}
	}elseif($action == 'approval_edit'){
		$sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '8' WHERE `planid` = '$planid'";

		$db->query($sql);
		if($db->affected_rows){
		//把对应的项目添加到付款列表中
		$plan_list_sql = "SELECT `db_funds_plan_list`.`accountid`,`db_funds_plan_list`.`plan_amount`,`db_material_account`.`amount`,`db_material_account`.`supplierid`,`db_material_invoice_list`.`invoice_no` FROM `db_funds_plan_list` INNER JOIN `db_material_account` ON `db_funds_plan_list`.`accountid` = `db_material_account`.`accountid` INNER JOIN `db_material_invoice_list` ON `db_material_invoice_list`.`accountid` = `db_material_account`.`accountid` WHERE `db_funds_plan_list`.`planid` = '$planid'";
		$res_plan = $db->query($plan_list_sql);
		if($res_plan->num_rows){
				while($row_plan = $res_plan->fetch_assoc()){
					$accountid = $row_plan['accountid'];
					$approval_date = date('Y-m-d');
					$amount = $row_plan['amount'];
					$apply_amount = $row_plan['plan_amount'];
					$invoice_no = $row_plan['invoice_no'];
					$supplierid = $row['supplierid'];
					$funds_list_sql = "INSERT INTO `db_material_funds_list`(`accountid`,`approval_date`,`amount`,`apply_amount`,`invoice_no`,`employeeid`,`approval_status`,`supplierid`) VALUES('$accountid','$approval_date','$amount','$apply_amount','$invoice_no','$employeeid','Z','$supplierid')";
					$db->query($funds_list_sql);
				}
			}
			header('location:material_funds_plan.php');
		
		}
	}
	?>