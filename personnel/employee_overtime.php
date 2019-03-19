<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01',strtotime("-1 month"));
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime(date('Y-m-01')."+1 month -1 day"));
if($_GET['submit']){
	$overtime_num = trim($_GET['overtime_num']);
	$applyer_name = trim($_GET['applyer_name']);
	$overtime_status = $_GET['overtime_status'];
	if($overtime_status != NULL){
		$sql_overtime_status = " AND `db_employee_overtime`.`overtime_status` = '$overtime_status'";
	}
	$sqlwhere = " AND `db_employee_overtime`.`overtime_num` LIKE '%$overtime_num%' AND `db_applyer`.`employee_name` LIKE '%$applyer_name%' $sql_overtime_status";
}
$sql = "SELECT `db_employee_overtime`.`overtimeid`,`db_employee_overtime`.`overtime_num`,`db_employee_overtime`.`confirmer`,`db_employee_overtime`.`apply_date`,`db_employee_overtime`.`start_time`,`db_employee_overtime`.`finish_time`,`db_employee_overtime`.`overtime`,`db_employee_overtime`.`overtime_valid`,`db_employee_overtime`.`overtime_status`,`db_applyer`.`employee_name` AS `applyer_name`,`db_confirmer`.`employee_name` AS `confirmer_name`,`db_department`.`dept_name` FROM `db_employee_overtime` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_overtime`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_applyer`.`deptid` LEFT JOIN `db_employee` AS `db_confirmer` ON `db_confirmer`.`employeeid` = `db_employee_overtime`.`confirmer` WHERE (`db_employee_overtime`.`apply_date` BETWEEN '$sdate' AND '$edate') AND `db_employee_overtime`.`approve_status` = 'B' $sqlwhere";
$result = $db->query($sql);
$_SESSION['employee_overtime_confirm'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_employee_overtime`.`overtimeid` DESC" . $pages->limitsql;
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
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>加班单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>加班单号：</th>
        <td><input type="text" name="overtime_num" class="input_txt" /></td>
        <th>申请人：</th>
        <td><input type="text" name="applyer_name" class="input_txt" /></td>
        <th>申请日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>状态：</th>
        <td><select name="overtime_status">
            <option value="">所有</option>
            <?php
			foreach($array_status as $status_key=>$status_value){
				echo "<option value=\"".$status_key."\">".$status_value."</option>";
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_employee_overtime.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">加班单号</th>
      <th width="8%">部门</th>
      <th width="8%">申请人</th>
      <th width="8%">申请日期</th>
      <th width="12%">开始时间</th>
      <th width="12%">结束时间</th>
      <th width="8%">加班<br />
        小时(H)</th>
      <th width="8%">有效<br />
        小时(H)</th>
      <th width="8%">确认人</th>
      <th width="4%">状态</th>
      <th width="4%">Confirm</th>
      <th width="4%">Timedo</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$overtimeid = $row['overtimeid'];
		$confirmer = $row['confirmer'];
		$confirmer_name = $confirmer?$row['confirmer_name']:'--';
		$overtime_valid = $row['overtime_valid'];
		$overtime_status = $row['overtime_status'];
	?>
    <tr>
      <td><?php echo $overtimeid; ?></td>
      <td><?php echo $row['overtime_num']; ?></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><?php echo $row['applyer_name']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $row['start_time']; ?></td>
      <td><?php echo $row['finish_time']; ?></td>
      <td><?php echo $row['overtime']; ?></td>
      <td><?php echo $overtime_valid; ?></td>
      <td><?php echo $confirmer_name; ?></td>
      <td><?php echo $array_status[$overtime_status]; ?></td>
      <td><?php if($confirmer == $employeeid || $confirmer == 0){ ?><a href="employee_overtime_confirm.php?id=<?php echo $overtimeid; ?>"><img src="../images/system_ico/confirm_10_10.png" width="10" height="10" /></a><?php } ?></td>
      <td><?php if($overtime_valid > 0 && $overtime_status == 1 && $confirmer){ ?><a href="employee_overtime_leavetime.php?id=<?php echo $overtimeid; ?>"><img src="../images/system_ico/switch_10_10.png" width="10" height="10" /></a><?php } ?></td>
      <td><a href="employee_overtime_info.php?id=<?php echo $overtimeid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
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