<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../config/config.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql_employee = "SELECT `db_employee`.`employee_name`,`db_employee`.`account`,`db_employee`.`employee_number`,`db_employee`.`phone`,`db_employee`.`extnum`,`db_employee`.`email`,`db_employee`.`photo_filedir`,`db_employee`.`photo_filename`,`db_department`.`dept_name`,`db_personnel_position`.`position_name`,`db_superior`.`employee_name` AS `superior_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_employee`.`positionid` LEFT JOIN `db_employee` AS `db_superior` ON `db_superior`.`employeeid` = `db_employee`.`superior` WHERE `db_employee`.`employeeid` = '$employeeid'";
$result_employee = $db->query($sql_employee);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/base.css?v=513" type="text/css" rel="stylesheet" />
<link href="../css/myjtl.css?v=521" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#myjtl_work_list p:first").css({'border-top':'none'});
	$("#myjtl_work_list p").click(function(){
		var id = $(this).attr('id');
		var display = $("#"+id+"_list").css('display');
		if(display == 'none'){
			$("#"+id+"_list").show();
			$(this).css({'border-bottom':'1px solid #DDD','background':'#F2F2F2'});
		}else{
			$("#"+id+"_list").hide();
			$(this).css({'border-bottom':'none'});
		}
		$("#myjtl_work_list ul").not($("#"+id+"_list")).hide();
		$("#myjtl_work_list p").not($("#"+id)).css({'border-bottom':'none','background':'#F9F9F9'});
	})
	$('.project_data').live('click',function(){
		var id = $(this).attr('id');
		var showid = id.substr(id.lastIndexOf('_')+1);
		var data = {showid:showid};
		$.post('../ajax_function/change_project_data_status.php',data,function(){
			console.log(data);
		})
	})
})
</script>
<title>希尔林信息平台</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="myjtl_tag">
  <!-- <h4>MY WORK >></h4> -->
</div>
<div id="myjtl">
  <div id="myjtl_left">
    <div id="myjtl_work_list">
      <?php
	  //我的出门证未审批
	  $sql_my_goout = "SELECT `gooutid`,`goout_num`,`apply_date` FROM `db_employee_goout` WHERE `approve_status` = 'A' AND `goout_status` = 1 AND `applyer` = '$employeeid'";
	  $result_my_goout = $db->query($sql_my_goout);
	  //我的请假未审批
	  $sql_my_leave = "SELECT `leaveid`,`leave_num`,`apply_date` FROM `db_employee_leave` WHERE `approve_status` = 'A' AND `leave_status` = 1 AND `applyer` = '$employeeid'";
	  $result_my_leave = $db->query($sql_my_leave);
	  //我的加班未审批
	  $sql_my_overtime = "SELECT `overtimeid`,`overtime_num`,`apply_date` FROM `db_employee_overtime` WHERE `approve_status` = 'A' AND `overtime_status` = 1 AND `applyer` = '$employeeid'";
	  $result_my_overtime = $db->query($sql_my_overtime);
	  //我用车未审批
	  $sql_my_vehicle = "SELECT `listid`,`vehicle_num`,`apply_date` FROM `db_vehicle_list` WHERE `approve_status` = 'A' AND `vehicle_status` = 1 AND `applyer` = '$employeeid'";
	  $result_my_vehicle = $db->query($sql_my_vehicle);
	  //我的快递未审批
	  $sql_my_express = "SELECT `expressid`,`express_num`,`apply_date` FROM `db_employee_express` WHERE `approve_status` = 'A' AND `express_status` = 1 AND `applyer` = '$employeeid'";
	  $result_my_express = $db->query($sql_my_express);
	  //我的试模未审批
	  $sql_my_mould_try = "SELECT `db_mould_try`.`tryid`,`db_mould_try`.`plan_date`,`db_mould`.`mould_number` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` WHERE `db_mould_try`.`approve_status` = 'A' AND `db_mould_try`.`try_status` = 1 AND `db_mould_try`.`employeeid` = '$employeeid'";
	  $result_my_mould_try = $db->query($sql_my_mould_try);
	
	  $total_apply = $result_my_goout->num_rows+$result_my_leave->num_rows+$result_my_overtime->num_rows+$result_my_vehicle->num_rows+$result_my_express->num_rows+$result_my_mould_try->num_rows;
	  //分割线-----------------------------------------------------------------------------------------------
	  //待审批出门证
	  $sql_goout = "SELECT `db_employee_goout`.`gooutid`,`db_employee_goout`.`goout_num`,`db_employee`.`employee_name` FROM `db_employee_goout` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_goout`.`applyer` WHERE `db_employee_goout`.`approve_status` = 'A' AND `db_employee_goout`.`goout_status` = 1 AND (`db_employee`.`superior` = '$employeeid' OR (`db_employee`.`position_type` IN ('A','B') AND `db_employee_goout`.`applyer` = '$employeeid'))";
	  $result_goout = $db->query($sql_goout);
	  //待审批请假
	  $sql_leave = "SELECT `db_employee_leave`.`leaveid`,`db_employee_leave`.`leave_num`,`db_applyer`.`employee_name` FROM `db_employee_leave` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_leave`.`applyer` WHERE `db_employee_leave`.`approve_status` = 'A' AND `db_employee_leave`.`leave_status` = 1 AND (`db_employee_leave`.`approver` = '$employeeid')";
	  $result_leave = $db->query($sql_leave);
	  //待审批加班
	  $sql_overtime = "SELECT `db_employee_overtime`.`overtimeid`,`db_employee_overtime`.`overtime_num`,`db_employee`.`employee_name` FROM `db_employee_overtime` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_overtime`.`applyer` WHERE `db_employee_overtime`.`approve_status` = 'A' AND `db_employee_overtime`.`overtime_status` = 1 AND `db_employee_overtime`.`approver` = '$employeeid'";
	  $result_overtime = $db->query($sql_overtime);
	  //待审批用车
	  $sql_vehicle = "SELECT `db_vehicle_list`.`listid`,`db_vehicle_list`.`vehicle_num`,`db_employee`.`employee_name` FROM `db_vehicle_list`INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_vehicle_list`.`applyer` INNER JOIN `db_vehicle_flow` ON `db_vehicle_flow`.`flowid` = `db_vehicle_list`.`flowid` WHERE (`db_vehicle_flow`.`approver` = '$employeeid' OR `db_vehicle_flow`.`certigier` = '$employeeid') AND `db_vehicle_list`.`vehicle_status` = 1";
	  $result_vehicle = $db->query($sql_vehicle);
	  //待审批快递
	  $sql_express = "SELECT `db_employee_express`.`expressid`,`db_employee_express`.`express_num`,`db_employee`.`employee_name` FROM `db_employee_express` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_express`.`applyer` WHERE `db_employee_express`.`approve_status` = 'A' AND `db_employee_express`.`express_status` = 1 AND (`db_employee`.`superior` = '$employeeid' OR (`db_employee`.`position_type` IN ('A','B') AND `db_employee_express`.`applyer` = '$employeeid'))";
	  $result_express = $db->query($sql_express);
	  //待申领快递
	  $sql_express_receive = "SELECT `expressid`,`express_num` FROM `db_employee_express_receive` WHERE `apply_status` = 0 AND `express_status` = 1 AND `receiver` = '$employeeid'";
	  $result_express_receive = $db->query($sql_express_receive);
	  //待试模审批
	  $sql_mould_try_approve = "SELECT `db_mould_try`.`tryid`,`db_mould`.`mould_number`,`db_employee`.`employee_name` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_try`.`employeeid` WHERE `db_mould_try`.`approve_status` = 'A' AND `db_mould_try`.`try_status` = 1 AND `db_mould_try`.`approver` = '$employeeid'";
	  $result_mould_try_approve = $db->query($sql_mould_try_approve);
	  //待审批期间物料
	  $sql_mould_other_material = "SELECT `db_employee`.`employee_name`,`db_mould_other_material`.`mould_other_id`,`db_other_material_specification`.`material_name`,`db_other_material_data`.`material_name` AS `data_name` FROM `db_mould_other_material` INNER JOIN `db_other_material_specification` ON `db_mould_other_material`.`material_name` = `db_other_material_specification`.`specificationid` INNER JOIN `db_employee` ON `db_mould_other_material`.`applyer` = `db_employee`.`employeeid` LEFT JOIN `db_other_material_data` ON `db_other_material_specification`.`materialid` = `db_other_material_data`.`dataid` WHERE `status` ='A' AND `approver` = '$employeeid' AND `approver` != 37";
	  $result_other_material = $db->query($sql_mould_other_material);
	  if($employeeid == 37){
		  $sql_total_other_material = "SELECT `db_employee`.`employee_name`,`db_mould_other_material`.`mould_other_id`,`db_other_material_specification`.`material_name`,`db_other_material_data`.`material_name` AS `data_name` FROM `db_mould_other_material` INNER JOIN `db_other_material_specification` ON `db_mould_other_material`.`material_name` = `db_other_material_specification`.`specificationid` INNER JOIN `db_employee` ON `db_mould_other_material`.`applyer` = `db_employee`.`employeeid` LEFT JOIN `db_other_material_data` ON `db_other_material_specification`.`materialid` = `db_other_material_data`.`dataid` WHERE `status` ='B' OR (`db_mould_other_material`.`approver` = '$employeeid' AND `db_mould_other_material`.`status` = 'A')";
		  $result_total_other_material = $db->query($sql_total_other_material);
		}
	  //外协订单未回
	   $processing_isadmin = $_SESSION['system_shell']['/mould_processing/']['isadmin'];
	   if($processing_isadmin == '1'){
	  	  $sql_outward_no_back = "SELECT * FROM `db_outward_inquiry_orderlist`  INNER JOIN `db_outward_inquiry` ON `db_outward_inquiry`.`inquiryid` = `db_outward_inquiry_orderlist`.`inquiryid` INNER JOIN `db_mould_material` ON `db_outward_inquiry`.`materialid` = `db_mould_material`.`materialid` WHERE  (`back_date` = '' AND (`plan_date` - CURDATE()) < 2) OR (`back_date` IS NULL AND (`plan_date` - CURDATE()) < 2)";
	   }else{
	   	 $sql_outward_no_back = "SELECT * FROM `db_outward_inquiry_orderlist` INNER JOIN `db_outward_inquiry` ON `db_outward_inquiry`.`inquiryid` = `db_outward_inquiry_orderlist`.`inquiryid` INNER JOIN `db_mould_material` ON `db_outward_inquiry`.`materialid` = `db_mould_material`.`materialid` WHERE (`db_outward_inquiry_orderlist`.`back_date` = '' AND (`db_outward_inquiry_orderlist`.`plan_date` - CURDATE()) < 2 AND `db_outward_inquiry`.`employeeid` = '$employeeid') OR (`db_outward_inquiry_orderlist`.`back_date` IS NULL AND (`db_outward_inquiry_orderlist`.`plan_date` - CURDATE()) < 2 AND `db_outward_inquiry`.`employeeid` = '$employeeid')";
	   }
	   $result_outward_no_back = $db->query($sql_outward_no_back);
	  //付款计划采购审核
	  $purchase_isconfirm = $_SESSION['system_shell']['/material_purchase/']['isconfirm'];
	  if($purchase_isconfirm == '1'){
		  $sql_funds_plan = "SELECT * FROM `db_material_funds_plan` WHERE `plan_status` = '0'";
		  $result_funds_plan = $db->query($sql_funds_plan);
		  $purchase_isconfirm_num = $result_funds_plan->num_rows;
		  //采购付款审核
		  $sql_funds_pay = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` = 'B' GROUP BY `planid`";
		  $result_funds_pay = $db->query($sql_funds_pay);
		  $pay_isconfirm_num = $result_funds_pay->num_rows;
		}else{
			$purchase_isconfirm_num = 0;
			$pay_isconfirm_num = 0;
		}
	  //付款计划总经办
	  $purchase_isadmin = $_SESSION['system_shell']['/material_purchase/']['isadmin'];
	  if($purchase_isadmin == '1'){
	  	$sql_funds_plan = "SELECT * FROM `db_material_funds_plan` WHERE `plan_status` = '3'";
	  	$result_funds_plan = $db->query($sql_funds_plan);
	  	$purchase_isadmin_num = $result_funds_plan->num_rows;
	  	//付款审核
	  	$sql_funds_pay = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` = 'D' GROUP BY `planid`";
	  	$result_pay = $db->query($sql_funds_pay);
	  	$pay_num = $result_pay->num_rows;
	  }else{
	  	$purchase_isadmin_num = 0;
	  	$pay_num = 0;
	  }
	  //付款计划财务审核
	  $financial_isconfirm = $_SESSION['system_shell']['/financial_management/']['isconfirm'];
	  if($financial_isconfirm == '1'){
	  	$sql_funds_plan = "SELECT * FROM `db_material_funds_plan` WHERE `plan_status` = '1'";
	  	$result_funds_plan = $db->query($sql_funds_plan);
	  	$financial_isconfirm_num = $result_funds_plan->num_rows;
	  	//财务付款审核
	  	$sql_funds_pay = "SELECT * FROM `db_funds_plan_list` WHERE `plan_status` = 'C' GROUP BY `planid`";
	  	$result_funds_pay = $db->query($sql_funds_pay);
	  	$pay_financial_num = $result_funds_pay->num_rows;
	    //财务对账审核
	    $sql_financial_account = "SELECT * FROM `db_material_account` WHERE `status` = 'F'";
	    $result_financial_account = $db->query($sql_financial_account);
	    $financial_account_num = $result_financial_account->num_rows;
	    //财务发票接收
		$sql_invoice = "SELECT * FROM `db_material_account` INNER JOIN `db_material_invoice_list` ON `db_material_account`.`accountid` = `db_material_invoice_list`.`accountid` WHERE `db_material_account`.`status` = 'I' AND `db_material_invoice_list`.`status` = 'A'";
		$result_invoice = $db->query($sql_invoice);
		$invoice_num = $result_invoice->num_rows;	    
	  }else{
	  	$financial_isconfirm_num = 0;
	  	$pay_financial_num = 0;
	  	$financial_account_num = 0;
	  	$invoice_num = 0;
	  }
	   //未审批设计图纸联络单
	  $isadmin_design = $_SESSION['system_shell']['/project_design/']['isadmin'];
	  $mould_change_number = 0;
	  if($isadmin_design == '1'){
	  	$sql_mould_change = "SELECT `specification_id`,`changeid`,`document_no` FROM `db_mould_change` WHERE `check` = '0'";
	  	$result_mould_change = $db->query($sql_mould_change);
	  	$mould_change_number = $result_mould_change->num_rows;
	  }
	  $total_approve = $result_goout->num_rows+$result_leave->num_rows+$result_overtime->num_rows+$result_vehicle->num_rows+$result_express->num_rows+$result_express_receive->num_rows+$result_mould_try_approve->num_rows+$result_other_material->num_rows+$purchase_isadmin_num+$purchase_isconfirm_num+$financial_isconfirm_num+$pay_isconfirm_num+$pay_financial_num+$pay_num+$financial_account_num+$invoice_num+$result_total_other_material->num_rows+$result_outward_no_back->num_rows+$mould_change_number;
	  //计划任务
	  $sql_plan = "SELECT `planid`,`plan_content`,`start_date` FROM `db_job_plan` WHERE `employeeid` = '$employeeid' AND `plan_status` = 1 AND `plan_result` = 0";
	  $result_plan = $db->query($sql_plan);
	  $total_plan = $result_plan->num_rows;
	  $sql_outward = "SELECT * FROM `db_outward_inquiry` WHERE `approver` = '$employeeid' AND `status` = '0'";
	 $result_outward = $db->query($sql_outward);
	 $outward_num = $result_outward->num_rows > 0?'1':'0';
	 //待查看项目资料
	 $sql_project_data = "SELECT `id`,`data_name`,`informationid`,`file_type`,`modifyid` FROM `db_mould_data_show` WHERE `employeeid` = '$employeeid' AND `status` = '0'";
	 $result_project_data = $db->query($sql_project_data);
	 //发起项目
	 $sql_data_begin = "SELECT `data_name`,`informationid`,`modifyid` FROM `db_mould_data_show` WHERE `status` = '0' GROUP BY `data_name`";
	 $result_data_begin = $db->query($sql_data_begin);
	 $total_plan = $result_plan->num_rows+$outward_num;
	 $total_note = $result_project_data->num_rows;
	 $total_begin = $result_data_begin->num_rows;
	  ?>
      <h4>日常工作</h4>
      <p id="my_apply"<?php echo $total_apply?' style="color:#F00;"':'' ?>>【我的申请】您有<span class="tasknum"><?php echo $total_apply; ?></span>个申请待审批</p>
      <ul id="my_apply_list" style="display:none;">
        <?php if($total_apply){ ?>
        <?php
		if($result_my_goout->num_rows){
		   while($row_my_goout = $result_my_goout->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_goout_info.php?id=<?php echo $row_my_goout['gooutid']; ?>">【出门】<?php echo date('m-d',strtotime($row_my_goout['apply_date'])).'/出门证号:'.$row_my_goout['goout_num']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		if($result_my_leave->num_rows){
			while($row_my_leave = $result_my_leave->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_leave_info.php?id=<?php echo $row_my_leave['leaveid']; ?>">【请假】<?php echo date('m-d',strtotime($row_my_leave['apply_date'])).'/请假单号:'.$row_my_leave['leave_num']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		if($result_my_overtime->num_rows){
			while($row_my_overtime = $result_my_overtime->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_overtime_info.php?id=<?php echo $row_my_overtime['overtimeid']; ?>">【加班】<?php echo date('m-d',strtotime($row_my_overtime['apply_date'])).'/加班单号:'.$row_my_overtime['overtime_num']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		if($result_my_vehicle->num_rows){
			while($row_my_vehicle = $result_my_vehicle->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_vehicle_info.php?id=<?php echo $row_my_vehicle['listid']; ?>">【用车】<?php echo date('m-d',strtotime($row_my_vehicle['apply_date'])).'/派车单号:'.$row_my_vehicle['vehicle_num']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		if($result_my_express->num_rows){
			while($row_my_express = $result_my_express->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_express_info.php?id=<?php echo $row_my_express['expressid']; ?>">【快递】<?php echo date('m-d',strtotime($row_my_express['apply_date'])).'/快递单号:'.$row_my_express['express_num']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		if($result_my_mould_try->num_rows){
			while($row_my_mould_try = $result_my_mould_try->fetch_assoc()){
		?>
        <li><a href="/project_management/mould_try_info.php?id=<?php echo $row_my_mould_try['tryid']; ?>">【试模】<?php echo date('m-d',strtotime($row_my_mould_try['plan_date'])).'/模具编号:'.$row_my_mould_try['mould_number']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		}else{
			echo "<li>【申请】暂无</li>";
		}
		?>
      </ul>
      <p id="my_approve"<?php echo $total_approve?' style="color:#F00;"':'' ?>>【我的审批】您有<span class="tasknum"><?php echo $total_approve; ?></span>个审批未处理</p>
      <ul id="my_approve_list" style="display:none;">
        <?php  if($total_approve){ ?>
        <?php
		if($result_goout->num_rows){
			while($row_goout = $result_goout->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_goout_approve.php?id=<?php echo $row_goout['gooutid']; ?>">【出门】<?php echo $row_goout['employee_name'].'/出门证号:'.$row_goout['goout_num']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		if($result_leave->num_rows){
			while($row_leave = $result_leave->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_leave_approve.php?id=<?php echo $row_leave['leaveid']; ?>">【请假】<?php echo $row_leave['employee_name'].'/请假单号:'.$row_leave['leave_num']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		if($result_overtime->num_rows){
			while($row_overtime = $result_overtime->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_overtime_approve.php?id=<?php echo $row_overtime['overtimeid']; ?>">【加班】<?php echo $row_overtime['employee_name'].'/加班单号:'.$row_overtime['overtime_num']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		if($result_vehicle->num_rows){
			while($row_vehicle = $result_vehicle->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_vehicle_approve.php?id=<?php echo $row_vehicle['listid']; ?>">【用车】<?php echo $row_vehicle['employee_name'].'/派车单号:'.$row_vehicle['vehicle_num']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		if($result_express->num_rows || $result_express_receive->num_rows){
			if($result_express->num_rows){
				while($row_express = $result_express->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_express_approve.php?id=<?php echo $row_express['expressid']; ?>">【快递】<?php echo $row_express['employee_name'].'/快递单号:'.$row_express['express_num']; ?></a></li>
        <?php
			  }
		  }
		  if($result_express_receive->num_rows){
				while($row_express_receive = $result_express_receive->fetch_assoc()){
		?>
        <li><a href="/my_office/employee_express_receive_apply.php?id=<?php echo $row_express_receive['expressid']; ?>">【快递】<?php echo $row_express_receive['inc_cname'].'/'.$row_express_receive['express_num']; ?></a></li>
        <?php
				}
			}
		}
		?>
        <?php
		if($result_mould_try_approve->num_rows){
			while($row_mould_try_approve = $result_mould_try_approve->fetch_assoc()){
		?>
        <li><a href="/project_management/mould_try_approve.php?id=<?php echo $row_mould_try_approve['tryid']; ?>">【试模】<?php echo $row_mould_try_approve['employee_name'].'/模具编号:'.$row_mould_try_approve['mould_number']; ?></a></li>
        <?php
			}
		}
		?>
				<?php
			if($purchase_isconfirm_num>0){
		?>
			<li><a href="/material_purchase/material_funds_plan.php">付款计划<?php echo $purchase_isconfirm_num ?>项</a></li>
		<?php }?>
		<?php
			if($purchase_isadmin_num>0){
		?>
			<li><a href="/material_purchase/material_funds_plan.php">付款计划<?php echo $purchase_isadmin_num ?>项</a></li>
		<?php } ?>
		<?php
			if($financial_isconfirm_num>0){
		?>
			<li><a href="/financial_management/material_funds_plan.php">付款计划<?php echo $financial_isconfirm_num ?>项</a></li>
		<?php } ?>
		<?php
			if($invoice_num >0){
		?>
			<li><a href="/financial_management/material_invoice_manage.php">发票接收<?php echo $invoice_num; ?>项</a></li>
		<?php } ?>
		<?php
			if($financial_account_num>0){
		?>
			<li><a href="/financial_management/material_balance_account.php">付款计划<?php echo $financial_account_num ?>项</a></li>
		<?php } ?>
		<?php
			if($pay_isconfirm_num >0){
		?>
			<li><a href="/material_purchase/material_funds_plan.php">付款审核<?php echo $pay_isconfirm_num ?>项</a></li>
		<?php } ?>
		<?php 
			if($pay_financial_num>0){
		?>
			<li><a href="/financial_management/material_funds_plan.php">付款审核<?php echo $pay_financial_num ?>项</a></li>
		<?php } ?>
		<?php
			if($pay_num >0){
		?>
			<li><a href="/material_purchase/material_funds_plan.php">付款审批<?php echo $pay_num; ?>项</a></li>
		<?php }?>
	    <?php
			if($result_other_material->num_rows){
				while($row_other_material = $result_other_material->fetch_assoc()){
					$material_name = $row_other_material['material_name']?$row_other_material['material_name']:$row_other_material['data_name'];
		?>
		<li><a href="/mould_material/mould_other_material_apply.php?action=edit&to=B&id=<?php echo $row_other_material['mould_other_id'] ?>"><?php echo '【期间物料】'.$row_other_material['employee_name'].'/'.$material_name ?></a></li>
		 <?php
			}
		}
		?>
		<?php
			if($result_total_other_material->num_rows){
				while($total_other_material = $result_total_other_material->fetch_assoc()){
					$material_name = $total_other_material['material_name']?$total_other_material['material_name']:$total_other_material['data_name'];
		?>
		<li><a href="/mould_material/mould_other_material_apply.php?action=edit&to=C&id=<?php echo $total_other_material['mould_other_id'] ?>"><?php echo '【期间物料】'.$total_other_material['employee_name'].'/'.$material_name ?></a></li>
		 <?php
			}
		}
		?>
		<?php
			if($result_outward_no_back->num_rows){
				while($row_outward_no_back = $result_outward_no_back->fetch_assoc()){
					
		?>
		<li><a href="/mould_processing/mould_outward_order_list.php?id=<?php echo $row_outward_no_back['listid'] ?>"><?php echo '【外协加工】'.$row_outward_no_back['material_name'].'/'.$row_outward_no_back['specification'] ?></a></li>
		 <?php
			}
		}
		?>
		 <?php
		if($result_mould_change->num_rows){
			while($row_mould_change = $result_mould_change->fetch_assoc()){
		?>
        <li><a href="/project_design/mould_change_edit.php?action=edit&specification_id=<?php echo $row_mould_change['specification_id']; ?>&changeid=<?php echo $row_mould_change['changeid'] ?>">【模具更改联络单】<?php echo '文件编号:'.$row_mould_change['document_no']; ?></a></li>
        <?php
			}
		}
		?>
        <?php
		}else{
			echo "<li>【审批】暂无</li>";
		}
		?>
      </ul>
      <p id="my_plan"<?php echo $total_plan?' style="color:#F00;"':'' ?>>【工作计划】您有<span class="tasknum"><?php echo $total_plan; ?></span>个计划未完成</p>
      <ul id="my_plan_list" style="display:none;">
      <?php if($total_plan >0){?>
        <?php
		if($result_plan->num_rows){
			while($row_plan = $result_plan->fetch_assoc()){
		?>
        <li><a href="/my_office/job_planae.php?id=<?php echo $row_plan['planid']; ?>&action=edit">【<?php echo $row_plan['start_date']; ?>】<?php echo strlen_sub($row_plan['plan_content'],10,10)?></a></li>
        <?php
			}
		}
		?>
		<?php
		if($result_outward->num_rows){
		?>
        <li><a href="/mould_processing/outward_inquiry_list.php">【待外协加工】 <?php echo $result_outward->num_rows;?>项</a></li>
        <?php
			}
		?>
		<?php
		}else{
			echo "<li>【计划】暂无</li>";
		}
		?>
      </ul>
       <p id="my_note"<?php echo $total_note?' style="color:#F00;"':'' ?>>【工作通知】您有<span class="tasknum"><?php echo $total_note; ?></span>条通知未查看</p>
      <ul id="my_note_list" style="display:none;">
      <?php if($total_note >0){?>
		<?php if($result_project_data->num_rows){ 
        	while($project_data = $result_project_data->fetch_assoc()){
        		if($project_data['modifyid'] == '0'){
        ?>	
			<li>
				<a class="project_data" id="project_data_<?php echo $project_data['id'] ?>"  href="/project_management/technical_data_list.php?action=show&data=<?php echo $project_data['data_name'] ?>&informationid=<?php echo $project_data['informationid'] ?>">您有一项<?php echo (data_name($project_data['data_name'],$array_mould_modify,$array_design_out,$array_processing_data,$array_quality_data,$array_project_data_type)) ?>待查看</a>
			</li>
		<?php }else{ ?>
			<li>
				<a class="project_data" id="project_data_<?php echo $project_data['id'] ?>"  href="/project_management/mould_modify_show.php?data=<?php echo $project_data['data_name'] ?>&action=show&modify_id=<?php echo $project_data['modifyid'] ?>">您有一项<?php echo $array_mould_modify[$project_data['data_name']] ?>待查看</a>
			</li>
        <?php }} }?>
		<?php
		}else{
			echo "<li>【通知】暂无</li>";
		}
		?>
      </ul>
       <p id="my_begin"<?php echo $total_begin?' style="color:#F00;"':'' ?>>【我的发起】您有<span class="tasknum"><?php echo $total_begin; ?></span>条发起未完成</p>
      <ul id="my_begin_list" style="display:none;">
      <?php if($total_begin >0){?>
		<?php if($result_data_begin->num_rows){ 
        	while($data_begin = $result_data_begin->fetch_assoc()){
        		foreach($array_project_data_type as $data_team){
        			if(array_key_exists($data_begin['data_name'],$data_team[1])){
        				$show_data = $data_team[1][$data_begin['data_name']];
					}
        		}
        ?>
        	<li>
				<a href="/project_management/technical_data_list.php?action=show&data=<?php echo $data_begin['data_name'] ?>&informationid=<?php echo $data_begin['informationid'] ?>">您有一项<?php echo $show_data; ?>待完成</a>
			</li>
        <?php } }?>
		<?php
		}else{
			echo "<li>【发起】暂无</li>";
		}
		?>
      </ul>
    </div>
    <?php 
	//PDCA工作
	$sql_pdca = "SELECT * FROM `db_work` WHERE `worker` = '$employeeid' AND `work_status` = 1 AND `pdca_status` NOT IN ('C','A')";
	$result_pdca = $db->query($sql_pdca);
	
    //未完成试模
	$sql_mould_try_finish = "SELECT * FROM `db_mould_try` WHERE `try_status` = 1 AND `approve_status` = 'B' AND `finish_status` = 0";
	$result_mould_try_finish = $db->query($sql_mould_try_finish);
	?>
    <div id="myjtl_task_list">
      <h4>待办任务</h4>
      <ul>
        <?php if($result_pdca->num_rows){ ?>
        	<li>
        		<a href="/pdca/my_work.php">【工作任务】您有<?php echo $result_pdca->num_rows; ?>个任务未完成</a>
        	</li>       
        <?php
		}else{
			echo "<li class=\"msg\">【工作任务】暂无</li>";
		}?>
        <?php if($result_mould_try_finish->num_rows){ ?>
        <li><a href="/mould_processing/mould_try_finish_list.php?finish_status=0&submit=查询">【试模确认】总计<?php echo $result_mould_try_finish->num_rows; ?>次试模未完成</a></li>
        <?php
		}else{
			echo "<li class=\"msg\">【试模确认】暂无</li>";
		}?>
      </ul>
    </div>
    <?php
    if($result_employee->num_rows){
		//查询最后一次登录时间
		$sql_login_log = "SELECT `dotime` FROM `db_login_log` WHERE `employeeid` = '$employeeid' AND `login_status` = 'A' ORDER BY `logid` DESC LIMIT 1,1";
		$result_login_log = $db->query($sql_login_log);
		if($result_login_log->num_rows){
			$array_login_log = $result_login_log->fetch_assoc();
			$last_logintime = $array_login_log['dotime'];
		}else{
			$last_logintime = '--';
		}
		//获取员工照片
		$array_employee = $result_employee->fetch_assoc();
		$photo_filedir = $array_employee['photo_filedir'];
		$photo_filename = $array_employee['photo_filename'];
		$photo_path = "../upload/personnel/".$photo_filedir.'/'.$photo_filename;
		$photo = is_file($photo_path)?"<img src=\"".$photo_path."\" />":"<img src=\"../images/no_photo_98_140.png\" width=\"98\" height=\"140\" />";
	?>
    <div id="myjtl_employee">
      <h4>个人信息</h4>
      <dl>
        <dt><?php echo $photo; ?></dt>
        <dd><?php echo $array_employee['employee_number']; ?></dd>
        <dd><?php echo $array_employee['employee_name'].'('.$array_employee['account'].')'; ?></dd>
        <dd><?php echo $array_employee['dept_name']; ?></dd>
        <dd><?php echo $array_employee['position_name']; ?></dd>
        <dd>上级领导：<?php echo $array_employee['superior_name']; ?></dd>
        <dd><!-- 电话： --><?php echo $array_employee['phone']; ?></dd>
        <dd>分机：<?php echo $array_employee['extnum']; ?></dd>
        <dd>上次登录：<?php echo $last_logintime; ?></dd>
      </dl>
      <ul>
        <li><a href="employee_info.php">资料修改</a></li>
        <li><a href="account_password.php">密码修改</a></li>
        <li><a href="../passport/logout.php">退出</a></li>
      </ul>
    </div>
    <?php } ?>
  </div>
  <div id="myjtl_right">
    <?php
    foreach($array_system_type as $system_type_key=>$system_type_value){
		if($system_type_key == 'A'){ //我的系统
		    $sql_system = "SELECT `db_system`.`system_name`,`db_system`.`image_filedir`,`db_system`.`image_filename`,`db_system`.`system_dir` FROM `db_system_employee` INNER JOIN `db_system` ON `db_system`.`systemid` = `db_system_employee`.`systemid` WHERE `db_system`.`system_type` = '$system_type_key' AND `db_system`.`system_status` = 1 AND `db_system_employee`.`employeeid` = '$employeeid' ORDER BY `db_system`.`system_order` ASC,`db_system`.`systemid` ASC";
		}elseif($system_type_key == 'B'){ //公共系统
			$sql_system = "SELECT `system_name`,`image_filedir`,`image_filename`,`system_dir` FROM `db_system` WHERE `system_type` = '$system_type_key' AND `system_status` = 1 ORDER BY `system_order` ASC,`systemid` ASC";
		}
		// echo $sql_system;
		$result_system = $db->query($sql_system);
		
	?>
    <div id="myjtl_program_list">
      <h4><?php echo $system_type_value; ?></h4>
      <?php
      if($result_system->num_rows){
		  //最新公告
		  $sql_notice = "SELECT * FROM `db_notice` WHERE DATEDIFF(CURDATE(),DATE_FORMAT(`dotime`,'%Y-%m-%d')) <= 7 AND `notice_status` = 1";
		  $result_notice = $db->query($sql_notice);
		  while($row_system = $result_system->fetch_assoc()){
			  $image_filedir = $row_system['image_filedir'];
			  $image_filename = $row_system['image_filename'];
			  $image_filepath = "../upload/system/".$image_filedir.'/'.$image_filename;
			  $image_info = (is_file($image_filepath))?"<img src=\"".$image_filepath."\" />":"<img src=\"../images/no_image_60_60.png\" width=\"60\" height=\"60\" />";
	  ?>
      <dl>
        <dt><a href="<?php echo $row_system['system_dir']; ?>"><?php echo $image_info; ?></a></dt>
        <dd><?php echo $row_system['system_name']; ?><?php if($row_system['system_dir'] == '/notice/' && $result_notice->num_rows) echo "<font color=red>[".$result_notice->num_rows."]</font>"; ?></dd>
      </dl>
      <?php } ?>
      <div class="clear"></div>
      <?php } ?>
    </div>
    <?php } ?>
  </div>
  <div class="clear"></div>
</div>
<div id="footer">
  <p>CopyRight ©2019 Suzhou Hillion Technology Co.,Ltd All Rights Reserved.</p>
</div>

</body>
</html>