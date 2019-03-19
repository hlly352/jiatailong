<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
//查询请假员工
$sql_leave_employee = "SELECT `applyer` FROM `db_employee_leave` WHERE DATE_FORMAT(`start_time`,'%Y') = '$year' AND `approve_status` = 'B' AND `leave_status` = 1 AND `confirmer` != 0 GROUP BY `applyer`";
$result_leave_employee = $db->query($sql_leave_employee);
if($result_leave_employee->num_rows){
	while($row_leave_employee = $result_leave_employee->fetch_assoc()){
		$array_leave_employee[] = $row_leave_employee['applyer'];
	}
}else{
	$array_leave_employee = array();
}
//print_r($array_leave_employee);
//查询加班员工
$sql_overtime_employee = "SELECT `applyer` FROM `db_employee_overtime` WHERE DATE_FORMAT(`start_time`,'%Y') = '$year' AND `approve_status` = 'B' AND `overtime_status` = 1 AND `confirmer` != 0 GROUP BY `applyer`";
$result_overtime_employee = $db->query($sql_overtime_employee);
if($result_overtime_employee->num_rows){
	while($row_overtime_employee = $result_overtime_employee->fetch_assoc()){
		$array_overtime_employee[] = $row_overtime_employee['applyer'];
	}
}else{
	$array_overtime_employee = array();
}
//print_r($array_leave_employee);
//合并数组
$array_applyer = array_unique(array_merge($array_leave_employee,$array_overtime_employee));
$all_employeeid = fun_convert_checkbox($array_applyer);
$all_employeeid = rtrim($all_employeeid,',');
if($_GET['submit']){
	$employee_name = trim($_GET['employee_name']);
	$sqlwhere = " AND `db_employee`.`employee_name` LIKE '%$employee_name%'";
}
//读取员工
$sql = "SELECT `db_employee`.`employeeid`,`db_employee`.`employee_name`,`db_department`.`dept_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `db_employee`.`employeeid` IN ($all_employeeid) $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,10);
$sqllist = $sql . " ORDER BY `db_department`.`dept_order` ASC,`db_employee`.`employeeid` ASC" . $pages->limitsql;
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
  <h4>考勤年报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>员工：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
        <th>年份：</th>
        <td><input type="text" name="year" value="<?php echo $year; ?>" onfocus="WdatePicker({dateFmt:'yyyy',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_employeeid .= $row_id['employeeid'].','; 
	  }
	  $array_employeeid = rtrim($array_employeeid,',');
	  //查询请假
	  $sql_leave = "SELECT `applyer`,DATE_FORMAT(`start_time`,'%Y-%m') AS `month`,SUM(`leavetime`) AS `leavetime` FROM `db_employee_leave` WHERE DATE_FORMAT(`start_time`,'%Y') = '$year' AND `applyer` IN ($array_employeeid) AND `approve_status` = 'B' AND `leave_status` = 1 AND `confirmer` != 0 GROUP BY `applyer`,DATE_FORMAT(`start_time`,'%Y-%m')";
	  $result_leave = $db->query($sql_leave);
	  if($result_leave->num_rows){
		  while($row_leave = $result_leave->fetch_assoc()){
			  $array_leave[$row_leave['applyer'].'-'.$row_leave['month']] = $row_leave['leavetime'];
		  }
	  }else{
		  $array_leave = array();
	  }
	  //print_r($array_leave);
	  //查询加班
	  $sql_overtime = "SELECT `applyer`,DATE_FORMAT(`start_time`,'%Y-%m') AS `month`,SUM(`overtime`) AS `overtime` FROM `db_employee_overtime` WHERE DATE_FORMAT(`start_time`,'%Y') = '$year' AND `applyer` IN ($array_employeeid) AND `approve_status` = 'B' AND `overtime_status` = 1 AND `confirmer` != 0 GROUP BY `applyer`,DATE_FORMAT(`start_time`,'%Y-%m')";
	  $result_overtime = $db->query($sql_overtime);
	  if($result_overtime->num_rows){
		  while($row_overtime = $result_overtime->fetch_assoc()){
			  $array_overtime[$row_overtime['applyer'].'-'.$row_overtime['month']] = $row_overtime['overtime'];
		  }
	  }else{
		  $array_overtime = array();
	  }
	  //print_r($array_overtime);
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="6%">员工</th>
      <th width="8%">部门</th>
      <th width="6%">类型</th>
      <?php
      for($i=1;$i<=12;$i++){
		  echo "<th width=\"6%\">".$i."月</th>";
	  }
	  ?>
      <th width="4%">Chart</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$employeeid = $row['employeeid'];
	?>
    <tr>
      <td rowspan="3"><?php echo $employeeid; ?></td>
      <td rowspan="3"><?php echo $row['employee_name']; ?></td>
      <td rowspan="3"><?php echo $row['dept_name']; ?></td>
      <td>请假</td>
      <?php
      for($i=1;$i<=12;$i++){
		  $month = date('Y-m',strtotime($year.'-'.$i));
		  $leave_key = $employeeid.'-'.$month;
		  $leavetime = number_format(array_key_exists($leave_key,$array_leave)?$array_leave[$leave_key]:0,1);
		  echo "<td>".$leavetime."</td>";
	  }
	  ?>
      <td rowspan="3"><a href="graphical_report_attendance_year.php?id=<?php echo $employeeid; ?>&year=<?php echo $year; ?>" target="_blank"><img src="../images/system_ico/chart_10_10.png" width="10" height="10" /></a></td>
    </tr>
    <tr>
      <td>加班</td>
      <?php
      for($i=1;$i<=12;$i++){
		  $month = date('Y-m',strtotime($year.'-'.$i));
		  $overtime_key = $employeeid.'-'.$month;
		  $overtime = number_format(array_key_exists($overtime_key,$array_overtime)?$array_overtime[$overtime_key]:0,1);
		  echo "<td>".$overtime."</td>";
	  }
	  ?>
    </tr>
    <tr>
      <td>折算</td>
      <?php
      for($i=1;$i<=12;$i++){
		  $month = date('Y-m',strtotime($year.'-'.$i));
		  $key = $employeeid.'-'.$month;
		  $leavetime = number_format(array_key_exists($key,$array_leave)?$array_leave[$key]:0,1);
		  $overtime = number_format(array_key_exists($key,$array_overtime)?$array_overtime[$key]:0,1);
		  $difftime = number_format(($overtime - $leavetime),1);
		  if($difftime<0){
			  echo "<td bgcolor=\"#FF0000\">".$difftime."</td>";
		  }elseif($difftime>0){
			  echo "<td bgcolor=\"#00CC66\">".$difftime."</td>";
		  }else{
			  echo "<td>".$difftime."</td>";
		  } 
	  }
	  ?>
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