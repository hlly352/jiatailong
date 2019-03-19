<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$listid = fun_check_int($_GET['id']);
$sql = "SELECT `db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`dotype`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`passby`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`start_time`,`db_vehicle_list`.`finish_time`,`db_vehicle_list`.`apply_date`,`db_vehicle_list`.`pathtype`,`db_vehicle_list`.`charge`,`db_vehicle_list`.`charge_wait`,`db_vehicle_list`.`odometer_start`,`db_vehicle_list`.`odometer_finish`,(`db_vehicle_list`.`odometer_finish`-`db_vehicle_list`.`odometer_start`) AS `kilometres`,`db_vehicle_list`.`charge_toll`,`db_vehicle_list`.`charge_parking`,`db_vehicle_list`.`wait_time`,((`db_vehicle_list`.`odometer_finish`-`db_vehicle_list`.`odometer_start`)*`db_vehicle_list`.`charge`+`db_vehicle_list`.`wait_time`*`db_vehicle_list`.`charge_wait`+`db_vehicle_list`.`charge_toll`+`db_vehicle_list`.`charge_parking`) AS `total_amount`,(`db_vehicle_list`.`wait_time`*`db_vehicle_list`.`charge_wait`) AS `wait_amount`,`db_vehicle_list`.`approve_status`,`db_vehicle_list`.`vehicle_status`,`db_vehicle_list`.`other`,`db_vehicle_list`.`cause`,`db_vehicle_list`.`settle_time`,`db_vehicle_list`.`vehicleid`,`db_vehicle`.`plate_number`,`db_vehicle_list`.`confirmer_out`,`db_vehicle_list`.`confirmer_in`,`db_vehicle_list`.`reckoner`,`db_applyer`.`employee_name` AS `applyer_name`,`db_department`.`dept_name`,`db_reckoner`.`employee_name` AS `reckoner_name`,`db_confirmer_out`.`employee_name` AS `confirmer_outname`,`db_confirmer_in`.`employee_name` AS `confirmer_inname` FROM `db_vehicle_list` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_vehicle_list`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_list`.`deptid` LEFT JOIN `db_vehicle` ON `db_vehicle`.`vehicleid` = `db_vehicle_list`.`vehicleid` LEFT JOIN `db_employee` AS `db_reckoner` ON `db_reckoner`.`employeeid`  = `db_vehicle_list`.`reckoner` LEFT JOIN `db_employee` AS `db_confirmer_out` ON `db_confirmer_out`.`employeeid`  = `db_vehicle_list`.`confirmer_out` LEFT JOIN `db_employee` AS `db_confirmer_in` ON `db_confirmer_in`.`employeeid`  = `db_vehicle_list`.`confirmer_in` WHERE `db_vehicle_list`.`listid` = '$listid'";
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
<title>门卫管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$plate_number = $array['vehicleid']?$array['plate_number']:'未派车';
	$pathtype = array_key_exists($array['pathtype'],$array_vehicle_pathtype)?$array_vehicle_pathtype[$array['pathtype']]:'--';
	$confirmer_outname = $array['confirmer_out']?$array['confirmer_outname']:'--';
	$confirmer_inname = $array['confirmer_in']?$array['confirmer_inname']:'--';
	$reckoner_name = $array['reckoner']?$array['reckoner_name']:'--';
	$settle_time = $array['reckoner']?$array['settle_time']:'--';
?>
<div id="table_sheet">
  <h4>用车信息</h4>
  <form action="approvedo.php" name="approve_vehicle" method="post">
    <table>
      <tr>
        <th>派车单号：</th>
        <td><?php echo $array['vehicle_num'] ?></td>
        <th>部门：</th>
        <td><?php echo $array['dept_name']; ?></td>
        <th>申请人：</th>
        <td><?php echo $array['applyer_name']; ?></td>
        <th>申请日期：</th>
        <td><?php echo $array['apply_date']; ?></td>
      </tr>
      <tr>
        <th>出发地：</th>
        <td><?php echo $array['departure']; ?></td>
        <th>目的地：</th>
        <td><?php echo $array['destination']; ?></td>
        <th>途径地：</th>
        <td><?php echo $array['passby']; ?></td>
        <th>路程方式：</th>
        <td><?php echo $array_vehicle_roundtype[$array['roundtype']]; ?></td>
      </tr>
      <tr>
        <th>用车类型：</th>
        <td><?php echo $array_vehicle_dotype[$array['dotype']]; ?></td>
        <th>随车人员：</th>
        <td><?php echo $array['other']; ?></td>
        <th>预计出厂时间：</th>
        <td><?php echo $array['start_time']; ?></td>
        <th>预计返厂时间：</th>
        <td><?php echo $array['finish_time']; ?></td>
      </tr>
      <tr>
        <th>车辆车牌：</th>
        <td><?php echo $plate_number; ?></td>
        <th>里程类型：</th>
        <td><?php echo $pathtype; ?></td>
        <th>计费单价(元/公里)：</th>
        <td><?php echo $array['charge']; ?></td>
        <th>等候单价(元/小时)：</th>
        <td><?php echo $array['charge_wait']; ?></td>
      </tr>
      <tr>
        <th>出厂码表里程(Km)：</th>
        <td><?php echo $array['odometer_start']; ?></td>
        <th>进厂码表里程(Km)：</th>
        <td><?php echo $array['odometer_finish']; ?></td>
        <th>总里程(Km)：</th>
        <td><?php echo $array['kilometres']; ?></td>
        <th>等候时间(H)：</th>
        <td><?php echo $array['wait_time']; ?></td>
      </tr>
      <tr>
        <th>停车费(元)：</th>
        <td><?php echo $array['charge_parking']; ?></td>
        <th>过路费(元)：</th>
        <td><?php echo $array['charge_toll']; ?></td>
        <th>等候费用(元)：</th>
        <td><?php echo $array['wait_amount']; ?></td>
        <th>总费用(元)：</th>
        <td><?php echo $array['total_amount']; ?></td>
      </tr>
      <tr>
        <th>出厂经办人：</th>
        <td><?php echo $confirmer_outname; ?></td>
        <th>进厂经办人：</th>
        <td><?php echo $confirmer_inname; ?></td>
        <th>结算人：</th>
        <td><?php echo $reckoner_name; ?></td>
        <th>结算时间：</th>
        <td><?php echo $settle_time; ?></td>
      </tr>
      <tr>
        <th>审批状态：</th>
        <td><?php echo $array_office_approve_status[$array['approve_status']]; ?></td>
        <th>状态：</th>
        <td><?php echo $array_status[$array['vehicle_status']]; ?></td>
        <th>用车事由：</th>
        <td><?php echo $array['cause']; ?></td>
        <th>&nbsp;</th>
        <td>&nbsp;</td>
      </tr>
    </table>
  </form>
</div>
<?php
$sql_approve = "SELECT `db_office_approve`.`approveid`,`db_office_approve`.`approve_content`,`db_office_approve`.`approve_status`,`db_office_approve`.`dotime`,`db_employee`.`employee_name` FROM `db_office_approve` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_office_approve`.`approver` WHERE `db_office_approve`.`linkid` = '$listid' AND `db_office_approve`.`approve_type` = 'V' ORDER BY `db_office_approve`.`approveid` DESC";
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