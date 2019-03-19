<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$listid = fun_check_int($_GET['id']);
$sql = "SELECT `db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`dotype`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`start_time`,`db_vehicle_list`.`finish_time`,`db_vehicle_list`.`apply_date`,`db_vehicle_list`.`pathtype`,`db_vehicle_list`.`approve_status`,`db_vehicle_list`.`vehicle_status`,`db_vehicle_list`.`other`,`db_vehicle_list`.`cause`,`db_vehicle`.`plate_number`,`db_employee`.`employee_name`,`db_department`.`dept_name` FROM `db_vehicle_list` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_vehicle_list`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_list`.`deptid` INNER JOIN `db_vehicle` ON `db_vehicle`.`vehicleid` = `db_vehicle_list`.`vehicleid` WHERE `db_vehicle_list`.`listid` = '$listid' AND `db_vehicle_list`.`approve_status` = 'B'";
$result = $db->query($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<style>
@charset "utf-8";
/*Base_css*/
body, html {
	height:100%;
}
* {
	margin:0;
	padding:0;
	font-family:"微软雅黑", "宋体";
}
#main {
	width:775px;
	height:1130px;  /* 原高度1054px,去掉caption 高度 44px */
	margin:0 auto;/*table-layout:automatic;*/
}
#vehicle, #approve {
	width:100%;
	border-collapse:collapse;
}
#vehicle td {
	border:1px solid #000;
	font-size:13px;
	text-align:center;
	height:50px;
	width:12.5%;/*
	white-space:nowrap;
	overflow:hidden;
	*/
}
#approve caption {
	font-size:20px;
	padding:10px 0;
}
#approve td, #approve th {
	border:1px solid #000;
	font-size:13px;
	text-align:center;
	height:50px;
}
#approve th {
	height:28px;
	background:#CCC;
}
</style>
<title>用车申请单打印-希尔林</title>
</head>

<body>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
?>
<table id="main">
  <tr>
    <td valign="top"><table id="vehicle">
        <tr>
          <td colspan="3" rowspan="2" style="border:none; background:url(../images/logo/logo.png) no-repeat 10px center;">&nbsp;</td>
          <td colspan="2" rowspan="2" style="border:none; font-size:24px;">用车申请单</td>
          <td colspan="3" style="border:none; height:30px; text-align:right;"><?php echo $array['dept_name']; ?></td>
        </tr>
        <tr>
          <td colspan="3" style="border:none; height:30px; text-align:right;">派车单号：<?php echo $array['vehicle_num']; ?></td>
        </tr>
        <tr>
          <td>申请人</td>
          <td><?php echo $array['employee_name']; ?></td>
          <td>申请日期</td>
          <td><?php echo $array['apply_date']; ?></td>
          <td>出发地</td>
          <td><?php echo $array['departure']; ?></td>
          <td>目的地</td>
          <td><?php echo $array['destination']; ?></td>
        </tr>
        <tr>
          <td>用车类型</td>
          <td><?php echo $array_vehicle_dotype[$array['dotype']]; ?></td>
          <td>里程方式</td>
          <td><?php echo $array_vehicle_roundtype[$array['roundtype']]; ?></td>
          <td>预计出厂时间</td>
          <td><?php echo $array['start_time']; ?></td>
          <td>预计返厂时间</td>
          <td><?php echo $array['finish_time']; ?></td>
        </tr>
        <tr>
          <td>车辆车牌</td>
          <td><?php echo $array['plate_number']; ?></td>
          <td>里程类型</td>
          <td><?php echo $array_vehicle_pathtype[$array['pathtype']]; ?></td>
          <td>出厂码表里程(Km)</td>
          <td>&nbsp;</td>
          <td>进厂码表里程(Km)</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>停车费(元)</td>
          <td>&nbsp;</td>
          <td>过路费(元)</td>
          <td>&nbsp;</td>
          <td>等候时间(H)</td>
          <td>&nbsp;</td>
          <td>等候确认人</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td>出厂经办人</td>
          <td>&nbsp;</td>
          <td>进厂经办人</td>
          <td>&nbsp;</td>
          <td>审批状态</td>
          <td><?php echo $array_office_approve_status[$array['approve_status']]; ?></td>
          <td>申请状态</td>
          <td><?php echo $array_status[$array['vehicle_status']]; ?></td>
        </tr>
        <tr>
          <td>随车人员</td>
          <td><?php echo $array['other']; ?></td>
          <td>途径地</td>
          <td>&nbsp;</td>
          <td>用车事由</td>
          <td colspan="3"><?php echo $array['cause']; ?></td>
        </tr>
      </table>
      <br />
      <br />
      <?php
      $sql_approve = "SELECT `db_office_approve`.`approveid`,`db_office_approve`.`approve_content`,`db_office_approve`.`approve_status`,`db_office_approve`.`dotime`,`db_office_approve`.`approver`,`db_office_approve`.`certigier`,`db_approver`.`employee_name` AS `approver_name`,`db_certigier`.`employee_name` AS `certigier_name` FROM `db_office_approve` INNER JOIN `db_employee` AS `db_approver` ON `db_approver`.`employeeid` = `db_office_approve`.`approver` INNER JOIN `db_employee` AS `db_certigier` ON `db_certigier`.`employeeid` = `db_office_approve`.`certigier` WHERE `db_office_approve`.`linkid` = '$listid' AND `db_office_approve`.`approve_type` = 'V' ORDER BY `db_office_approve`.`approveid` DESC";
	  $result_approve = $db->query($sql_approve);
	  if($result_approve->num_rows){
	  ?>
      <table id="approve">
        <caption>
        审批意见
        </caption>
        <tr>
          <th width="8%">序号</th>
          <th width="25%">审批人</th>
          <th width="15%">审批结果</th>
          <th>审批意见</th>
          <th width="30%">审批时间</th>
        </tr>
        <?php
		$i = $result_approve->num_rows;
		while($row_approve = $result_approve->fetch_assoc()){
			$approver = ($row_approve['approver'] != $row_approve['certigier'])?$row_approve['approver_name'].'<br />('.$row_approve['certigier_name'].'授权)':$row_approve['approver_name'];
		?>
        <tr>
          <td><?php echo $i; ?></td>
          <td><?php echo $approver; ?></td>
          <td><?php echo $array_office_approve_status[$row_approve['approve_status']]; ?></td>
          <td><?php echo $row_approve['approve_content']; ?></td>
          <td><?php echo $row_approve['dotime']; ?></td>
        </tr>
        <?php
		$i--;
		}
		?>
      </table>
      <?php } ?></td>
  </tr>
</table>
<?php } ?>
</body>
</html>