<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
	$action = $_REQUEST['action'];
	$from = $_GET['from'];
	$planid = $_GET['planid'];
	$accountid = $_GET['accountid'];
	$order_sql = "SELECT `db_funds_plan_list`.`listid` FROM `db_funds_plan_list` INNER JOIN `db_account_order_list` ON `db_funds_plan_list`.`order_listid` = `db_account_order_list`.`listid`  INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_account_order_list`.`orderid` WHERE `db_funds_plan_list`.`planid` = '$planid' AND `db_account_order_list`.`accountid` = '$accountid'";
	$result_order = $db->query($order_sql);
	if($result_order->num_rows){
		while($row_order = $result_order->fetch_assoc()){
	 		$listid_array[] = $row_order['listid'];
		 	}
		 }
	$listid = fun_convert_checkbox($listid_array);
	if($from == 'financial'){
		if($action == 'complete'){
			 //���ļƻ������״̬
			 $sql = "UPDATE `db_funds_plan_list` SET `plan_status` = 'D' WHERE `listid` IN($listid)";
			 $db->query($sql);
			 //�жϵ�ǰ�ƻ��Ƿ���ȫͨ�����
			 $plan_sql = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status`='B' OR `plan_status` = 'A' OR `plan_status` = 'C' AND `planid` = '$planid'";
			 $result_plan= $db->query($plan_sql);
			 $count = $result_plan->num_rows;
			
			 if($count == 0){
			 	//���ļƻ�״̬
			 	$status_sql = "UPDATE `db_material_funds_plan` SET `plan_status` = '9' WHERE `planid`='$planid'";
			 	$db->query($status_sql);
			 	header('location:material_funds_plan.php');
			 }else{
			 	header('location:funds_pay_apply.php?action=purchase&id='.$planid);
			 }
		}elseif($action == 'back'){
			//�������
			$sql = "UPDATE `db_funds_plan_list` SET `plan_status` = 'B' WHERE `listid` IN($listid)";
			$db->query($sql);
			header('location:material_funds_plan.php');
			}
		}
	
		
		
	
	