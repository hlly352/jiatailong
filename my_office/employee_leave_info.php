<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$leaveid = fun_check_int($_GET['id']);
$sql = "SELECT `db_employee_leave`.`leaveid`,`db_employee_leave`.`leave_num`,`db_employee_leave`.`applyer`,`db_employee_leave`.`agenter`,`db_employee_leave`.`confirmer_out`,`db_employee_leave`.`confirmer_in`,`db_employee_leave`.`confirmer`,`db_employee_leave`.`apply_date`,`db_employee_leave`.`work_shift`,`db_employee_leave`.`start_time`,`db_employee_leave`.`finish_time`,`db_employee_leave`.`leavetime`,`db_employee_leave`.`leave_cause`,`db_employee_leave`.`approve_status`,`db_employee_leave`.`leave_status`,`db_employee_leave`.`dotime`,`db_employee_leave`.`confirmtime_out`,`db_employee_leave`.`confirmtime_in`,`db_employee_leave`.`confirm_time`,`db_personnel_vacation`.`vacation_name`,`db_applyer`.`employee_name` AS `applyer_name`,`db_agenter`.`employee_name` AS `agenter_name`,`db_confirmer_out`.`employee_name` AS `confirmer_outname`,`db_confirmer_in`.`employee_name` AS `confirmer_inname`,`db_confirmer`.`employee_name` AS `confirmer_name`,`db_department`.`dept_name` FROM `db_employee_leave` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_leave`.`applyer` INNER JOIN `db_employee` AS `db_agenter` ON `db_agenter`.`employeeid` = `db_employee_leave`.`agenter` LEFT JOIN `db_employee` AS `db_confirmer_out` ON `db_confirmer_out`.`employeeid` = `db_employee_leave`.`confirmer_out` LEFT JOIN `db_employee` AS `db_confirmer_in` ON `db_confirmer_in`.`employeeid` = `db_employee_leave`.`confirmer_in` LEFT JOIN `db_employee` AS `db_confirmer` ON `db_confirmer`.`employeeid` = `db_employee_leave`.`confirmer` INNER JOIN `db_personnel_vacation` ON `db_personnel_vacation`.`vacationid` = `db_employee_leave`.`vacationid` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_applyer`.`deptid` WHERE `db_employee_leave`.`leaveid` = '$leaveid'";
$result = $db->query($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$agenter_name = ($array['applyer'] == $array['agenter'])?'--':$array['agenter_name'];
	$confirmer_outname = $array['confirmer_out']?$array['confirmer_outname']:'--';
	$confirmer_inname = $array['confirmer_in']?$array['confirmer_inname']:'--';
	$confirmer_name = $array['confirmer']?$array['confirmer_name']:'--';
	$confirmtime_out = $array['confirmer_out']?$array['confirmtime_out']:'--';
	$confirmtime_in = $array['confirmer_in']?$array['confirmtime_in']:'--';
	$confirm_time = $array['confirmer']?$array['confirm_time']:'--';
?>
<div id="table_sheet">
  <h4>请假单信息</h4>
  <table>
    <tr>
      <th width="20%">请假单号：</th>
      <td width="80%"><?php echo $array['leave_num']; ?></td>
    </tr>
    <tr>
      <th>部门：</th>
      <td><?php echo $array['dept_name']; ?></td>
    </tr>
    <tr>
      <th>申请人：</th>
      <td><?php echo $array['applyer_name']; ?></td>
    </tr>
    <tr>
      <th>代理人：</th>
      <td><?php echo $agenter_name; ?></td>
    </tr>
    <tr>
      <th>申请日期：</th>
      <td><?php echo $array['apply_date']; ?></td>
    </tr>
    <tr>
      <th>班次：</th>
      <td><?php echo $array_work_shift[$array['work_shift']]; ?></td>
    </tr>
    <tr>
      <th>开始时间：</th>
      <td><?php echo $array['start_time']; ?></td>
    </tr>
    <tr>
      <th>结束时间：</th>
      <td><?php echo $array['finish_time']; ?></td>
    </tr>
    <tr>
      <th>小时(H)：</th>
      <td><?php echo $array['leavetime']; ?></td>
    </tr>
    <tr>
      <th>请假类型：</th>
      <td><?php echo $array['vacation_name']; ?></td>
    </tr>
    <tr>
      <th>请假事由：</th>
      <td><?php echo $array['leave_cause']; ?></td>
    </tr>
    <tr>
      <th>操作时间：</th>
      <td><?php echo $array['dotime']; ?></td>
    </tr>
    <tr>
      <th>审批状态：</th>
      <td><?php echo $array_office_approve_status[$array['approve_status']]; ?></td>
    </tr>
    <tr>
      <th>状态：</th>
      <td><?php echo $array_status[$array['leave_status']]; ?></td>
    </tr>
    <tr>
      <th>出厂确认人：</th>
      <td><?php echo $confirmer_outname; ?></td>
    </tr>
    <tr>
      <th>出厂确认时间：</th>
      <td><?php echo $confirmtime_out; ?></td>
    </tr>
    <tr>
      <th>回厂确认人：</th>
      <td><?php echo $confirmer_inname; ?></td>
    </tr>
    <tr>
      <th>出厂确认时间：</th>
      <td><?php echo $confirmtime_in; ?></td>
    </tr>
    <tr>
      <th>确认人：</th>
      <td><?php echo $confirmer_name; ?></td>
    </tr>
    <tr>
      <th>确认时间：</th>
      <td><?php echo $confirm_time; ?></td>
    </tr>
  </table>
</div>
<?php
$sql_approve = "SELECT `db_office_approve`.`approveid`,`db_office_approve`.`approve_content`,`db_office_approve`.`approve_status`,`db_office_approve`.`dotime`,`db_employee`.`employee_name` FROM `db_office_approve` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_office_approve`.`approver` WHERE `db_office_approve`.`linkid` = '$leaveid' AND `db_office_approve`.`approve_type` = 'L' ORDER BY `db_office_approve`.`approveid` DESC";
$result_approve = $db->query($sql_approve);
?>
<div id="table_list">
  <?php if($result_approve->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">审批人</th>
      <th>审批意见</th>
      <th width="10%">审批状态</th>
      <th width="10%">审批时间</th>
    </tr>
    <?php while($row_approve = $result_approve->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_approve['approveid']; ?></td>
      <td><?php echo $row_approve['employee_name']; ?></td>
      <td><?php echo $row_approve['approve_content']; ?></td>
      <td><?php echo $array_office_approve_status[$row_approve['approve_status']]; ?></td>
      <td><?php echo $row_approve['dotime']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无审批记录！</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>