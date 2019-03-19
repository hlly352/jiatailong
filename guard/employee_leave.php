<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01',strtotime("-1 month"));
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime(date('Y-m-01')."+1 month -1 day"));
if($_GET['submit']){
	$leave_num = trim($_GET['leave_num']);
	$applyer_name = trim($_GET['applyer_name']);
	$leave_status = $_GET['leave_status'];
	if($leave_status != NULL){
		$sql_leavestatus = " AND `db_employee_leave`.`leave_status` = '$leave_status'";
	}
	$sqlwhere = " AND `db_employee_leave`.`leave_num` LIKE '%$leave_num%' AND `db_applyer`.`employee_name` LIKE '%$applyer%' $sql_leavestatus";
}else{
	$leave_status = 1;
	$sqlwhere = " AND `db_employee_leave`.`leave_status` = '$leave_status'";
}
$sql = "SELECT `db_employee_leave`.`leaveid`,`db_employee_leave`.`leave_num`,`db_employee_leave`.`confirmer_out`,`db_employee_leave`.`confirmer_in`,`db_employee_leave`.`apply_date`,`db_employee_leave`.`start_time`,`db_employee_leave`.`finish_time`,`db_employee_leave`.`leavetime`,`db_employee_leave`.`work_shift`,`db_employee_leave`.`leave_status`,`db_personnel_vacation`.`vacation_name`,`db_applyer`.`employee_name` AS `applyer_name`,`db_confirmer_out`.`employee_name` AS `confirmer_outname`,`db_confirmer_in`.`employee_name` AS `confirmer_inname` FROM `db_employee_leave` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_leave`.`applyer` LEFT JOIN `db_employee` AS `db_confirmer_out` ON `db_confirmer_out`.`employeeid` = `db_employee_leave`.`confirmer_out` LEFT JOIN `db_employee` AS `db_confirmer_in` ON `db_confirmer_in`.`employeeid` = `db_employee_leave`.`confirmer_in` INNER JOIN `db_personnel_vacation` ON `db_personnel_vacation`.`vacationid` = `db_employee_leave`.`vacationid` WHERE (`db_employee_leave`.`apply_date` BETWEEN '$sdate' AND '$edate') AND `db_employee_leave`.`approve_status` = 'B' $sqlwhere";
$result = $db->query($sql);
$_SESSION['employee_leave_confirm'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_employee_leave`.`leaveid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>门卫管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>请假单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>请假单号：</th>
        <td><input type="text" name="leave_num" class="input_txt" /></td>
        <th>申请人：</th>
        <td><input type="text" name="applyer_name" class="input_txt" /></td>
        <th>申请日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>状态：</th>
        <td><select name="leave_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $leave_status && $leave_status != NULL) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_employee_leave.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">请假单号</th>
      <th width="8%">申请人</th>
      <th width="8%">申请日期</th>
      <th width="10%">开始时间</th>
      <th width="10%">结束时间</th>
      <th width="8%">小时(H)</th>
      <th width="8%">类型</th>
      <th width="8%">班次</th>
      <th width="8%">出厂确认人</th>
      <th width="8%">回厂确认人</th>
      <th width="4%">状态</th>
      <th width="4%">Confirm</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$leaveid = $row['leaveid'];
		$confirmer_out = $row['confirmer_out'];
		$confirmer_in = $row['confirmer_in'];
		$confirmer_outname = $confirmer_out?$row['confirmer_outname']:'--';
		$confirmer_inname = $confirmer_in?$row['confirmer_inname']:'--';
		$leave_status = $row['leave_status'];
	?>
    <tr>
      <td><?php echo $leaveid; ?></td>
      <td><?php echo $row['leave_num']; ?></td>
      <td><?php echo $row['applyer_name']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $row['start_time']; ?></td>
      <td><?php echo $row['finish_time']; ?></td>
      <td><?php echo $row['leavetime']; ?></td>
      <td><?php echo $row['vacation_name']; ?></td>
      <td><?php echo $array_work_shift[$row['work_shift']]; ?></td>
      <td><?php echo $confirmer_outname; ?></td>
      <td><?php echo $confirmer_inname; ?></td>
      <td><?php echo $array_status[$leave_status]; ?></td>
      <td><?php if(($confirmer_out == 0 || $confirmer_in == 0) && $leave_status){ ?><a href="employee_leave_confirm.php?id=<?php echo $leaveid; ?>"><img src="../images/system_ico/confirm_10_10.png" width="10" height="10" /></a><?php } ?></td>
      <td><a href="employee_leave_info.php?id=<?php echo $leaveid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>