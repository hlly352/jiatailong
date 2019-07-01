  <?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
//获取员工id
$employeeid = $_SESSION['employee_info']['employeeid'];

$sql_employee = "SELECT `db_employee`.`employee_name`,`db_employee`.`account`,`db_employee`.`employee_number`,`db_employee`.`phone`,`db_employee`.`extnum`,`db_employee`.`email`,`db_employee`.`photo_filedir`,`db_employee`.`photo_filename`,`db_department`.`dept_name`,`db_personnel_position`.`position_name`,`db_superior`.`employee_name` AS `superior_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_employee`.`positionid` LEFT JOIN `db_employee` AS `db_superior` ON `db_superior`.`employeeid` = `db_employee`.`superior` WHERE `db_employee`.`employeeid` = '$employeeid'";
$result_employee = $db->query($sql_employee);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/base.css?v=314" type="text/css" rel="stylesheet" />
<link href="../css/myjtl.css?v=314" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript">
//设置左侧边栏的点击效果
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
	  //待审批期间物料
	  $sql_mould_other_material = "SELECT * FROM `db_mould_other_material` WHERE `status` ='A' AND `approver` = '$employeeid'";
	  $result_other_material = $db->query($sql_mould_other_material);
	  //带审批报价
	  $sql_mould_quote_approve = "SELECT `db_mould_data`.`mould_dataid` FROM `db_mould_data` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = '5020'  INNER JOIN `db_system_employee` ON `db_system_employee`.`employeeid` = `db_employee`.`employeeid` WHERE `db_system_employee`.`systemid` = '19' AND `db_system_employee`.`isadmin`='1' AND `db_mould_data`.`is_approval`='0'";
	  //echo $sql_mould_quote_approve;
	  $result_mould_try_approve = $db->query($sql_mould_try_approve);
	  $total_approve = $result_goout->num_rows+$result_leave->num_rows+$result_overtime->num_rows+$result_vehicle->num_rows+$result_express->num_rows+$result_express_receive->num_rows+$result_mould_try_approve->num_rows+$result_other_material->num_rows;
	  //计划任务
	  $sql_plan = "SELECT `planid`,`plan_content`,`start_date` FROM `db_job_plan` WHERE `employeeid` = '$employeeid' AND `plan_status` = 1 AND `plan_result` = 0";
	  $result_plan = $db->query($sql_plan);
	  $total_plan = $result_plan->num_rows;
	  ?>
      <h4>日常工作</h4>
      <p id="my_apply"<?php echo $total_apply?' style="color:#F00;"':'' ?>>【我的申请】您有<?php echo $total_apply; ?>个申请待审批</p>
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
      <p id="my_approve"<?php echo $total_approve?' style="color:#F00;"':'' ?>>【我的审批】您有<?php echo $total_approve; ?>个审批未处理</p>
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
			if($result_other_material->num_rows){
				while($row_other_material = $result_other_material->fetch_assoc()){
					$name = getName($row_other_material['applyer'],$db);

		?>
		<li><a href="/mould_material/mould_other_material_apply.php?action=edit&id=<?php echo $row_other_material['mould_other_id'] ?>">【期间物料】<?php echo $name.'/物料名称：'.$row_other_material['material_name'] ?></a></li>
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
		}else{
			echo "<li>【审批】暂无</li>";
		}
		?>
      </ul>
      <p id="my_plan"<?php echo $total_plan?' style="color:#F00;"':'' ?>>【工作计划】您有<?php echo $total_plan; ?>个计划未完成</p>
      <ul id="my_plan_list" style="display:none;">
        <?php
		if($result_plan->num_rows){
			while($row_plan = $result_plan->fetch_assoc()){
		?>
        <li><a href="/my_office/job_planae.php?id=<?php echo $row_plan['planid']; ?>&action=edit">【<?php echo $row_plan['start_date']; ?>】<?php echo strlen_sub($row_plan['plan_content'],10,10)?></a></li>
        <?php
			}
		}else{
			echo "<li>【计划】暂无</li>";
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
        <li><a href="/pdca/my_work.php">【工作任务】您有<?php echo $result_pdca->num_rows; ?>个任务未完成</a></li>
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
        <dd>电话：<?php echo $array_employee['phone']; ?></dd>
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
		    $sql_system = "SELECT `db_system`.`systemid`,`db_system`.`system_name`,`db_system`.`image_filedir`,`db_system`.`image_filename`,`db_system`.`system_dir` FROM `db_system_employee` INNER JOIN `db_system` ON `db_system`.`systemid` = `db_system_employee`.`systemid` WHERE `db_system`.`system_type` = '$system_type_key' AND `db_system`.`system_status` = 1 AND `db_system_employee`.`employeeid` = '$employeeid' ORDER BY `db_system`.`system_order` ASC,`db_system`.`systemid` ASC";
		}elseif($system_type_key == 'B'){ //公共系统
			$sql_system = "SELECT `system_name`,`image_filedir`,`image_filename`,`system_dir` FROM `db_system` WHERE `system_type` = '$system_type_key' AND `system_status` = 1 ORDER BY `system_order` ASC,`systemid` ASC";
		}
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
        <dt><a href="<?php echo $row_system['system_dir'].'?system_id = '.$row_system['systemid']; ?>"><?php echo $image_info; ?></a></dt>
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
<?php include "../footer.php"; ?>
</body>
</html>