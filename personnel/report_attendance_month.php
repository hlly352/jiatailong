<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询请假
$sql_leave = "SELECT `applyer`,SUM(`leavetime`) AS `leavetime` FROM `db_employee_leave` WHERE (DATE_FORMAT(`start_time`,'%Y-%m-%d') BETWEEN '$sdate' AND '$edate') AND `approve_status` = 'B' AND `leave_status` = 1 AND `confirmer` != 0 GROUP BY `applyer`";
$result_leave = $db->query($sql_leave);
if($result_leave->num_rows){
	while($row_leave = $result_leave->fetch_assoc()){
		$array_leave[$row_leave['applyer']] = $row_leave['leavetime'];
	}
}else{
	$array_leave = array();
}
//print_r($array_leave);
//查询加班
$sql_overtime = "SELECT `applyer`,SUM(`overtime`) AS `overtime` FROM `db_employee_overtime` WHERE (DATE_FORMAT(`start_time`,'%Y-%m-%d') BETWEEN '$sdate' AND '$edate') AND `approve_status` = 'B' AND `overtime_status` = 1 AND `confirmer` != 0 GROUP BY `applyer`";
$result_overtime = $db->query($sql_overtime);
if($result_overtime->num_rows){
	while($row_overtime = $result_overtime->fetch_assoc()){
		$array_overtime[$row_overtime['applyer']] = $row_overtime['overtime'];
	}
}else{
	$array_overtime = array();
}
//print_r($array_overtime);
//合并
$array_applyer_leave = array_keys($array_leave);
$array_applyer_overtime = array_keys($array_overtime);
$array_applyer = array_unique(array_merge($array_applyer_leave,$array_applyer_overtime));
//print_r($array_applyer);
//列出用户
$array_employeeid = fun_convert_checkbox($array_applyer);
$array_employeeid = rtrim($array_employeeid,',');
if($_GET['submit']){
	$employee_name = trim($_GET['employee_name']);
	$sqlwhere = " AND `db_employee`.`employee_name` LIKE '%$employee_name%'";
}
$sql = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name`,`db_department`.`dept_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `db_employee`.`employeeid` IN ($array_employeeid) $sqlwhere";
$result = $db->query($sql);
$_SESSION['report_attendance_month'] = $sql;
$pages = new page($result->num_rows,12);
$sqllist = $sql . " ORDER BY `db_department`.`dept_order`,`db_employee`.`employeeid` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sqllist);
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
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>考勤月报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>员工：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
        <th>日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_report_attendance_month.php?sdate='+search.sdate.value+'&edate='+search.edate.value" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="15%">员工</th>
      <th width="15%">部门</th>
      <th width="30%">请假(H)</th>
      <th width="30%">加班(H)</th>
      <th width="6%">折算(H)</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$employeeid = $row['employeeid'];
		$leavetime = number_format(array_key_exists($employeeid,$array_leave)?$array_leave[$employeeid]:0,1);
		$overtime = number_format(array_key_exists($employeeid,$array_overtime)?$array_overtime[$employeeid]:0,1);
		$difftime = number_format(($overtime - $leavetime),1);
		if($difftime<0){
			$td_bg = " bgcolor=\"#FF0000\"";
		}elseif($difftime>0){
			$td_bg = " bgcolor=\"#00CC66\"";
		}else{
			$td_bg = "";
		}
	?>
    <tr>
      <td><?php echo $employeeid; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><?php echo $leavetime; ?></td>
      <td><?php echo $overtime; ?></td>
      <td<?php echo $td_bg; ?>><?php echo $difftime; ?></td>
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