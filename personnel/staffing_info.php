<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$positionid = fun_check_int($_GET['id']);
$year = $_GET['year']?$_GET['year']:date('Y');
//职位
$sql_position = "SELECT `position_name` FROM `db_personnel_position` WHERE `positionid` = '$positionid' AND `position_status` = 1";
$result_position = $db->query($sql_position);
//该职位编制部门
$sql_dept = "SELECT `db_personnel_staffing`.`deptid`,`db_department`.`dept_name` FROM `db_personnel_staffing` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_personnel_staffing`.`deptid` WHERE `db_personnel_staffing`.`positionid` = '$positionid' AND DATE_FORMAT(`db_personnel_staffing`.`month`,'%Y') = '$year' GROUP BY `db_personnel_staffing`.`deptid`";
$result_dept = $db->query($sql_dept);
if($result_dept->num_rows){
	while($row_dept = $result_dept->fetch_assoc()){
		$array_dept[$row_dept['deptid']] = $row_dept['dept_name'];
	}
}else{
	$array_dept = array();
}
//统计部门每月数量
$sql_month_staffing = "SELECT `deptid`,`month`,`quantity` FROM `db_personnel_staffing` WHERE `positionid` = '$positionid' AND DATE_FORMAT(`month`,'%Y') = '$year'";
$result_month_staffing = $db->query($sql_month_staffing);
if($result_month_staffing->num_rows){
	while($row_month_staffing = $result_month_staffing->fetch_assoc()){
		$array_month_staffing[$row_month_staffing['deptid'].'-'.$row_month_staffing['month']] = $row_month_staffing['quantity'];
	}
}else{
	$array_month_staffing = array();
}
//print_r($array_month_staffing);
//统计总数量
$sql_total_staffing = "SELECT `month`,SUM(`quantity`) AS `quantity` FROM `db_personnel_staffing` WHERE `positionid` = '$positionid' AND DATE_FORMAT(`month`,'%Y') = '$year' GROUP BY `month`";
$result_total_staffing = $db->query($sql_total_staffing);
if($result_total_staffing->num_rows){
	while($row_total_staffing = $result_total_staffing->fetch_assoc()){
		$array_total_staffing[$row_total_staffing['month']] = $row_total_staffing['quantity'];
	}
}else{
	$array_total_staffing = array();
}
//print_r($array_total_staffing);
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
<div id="table_list">
  <?php
  if($result_position->num_rows){
	  $array_position = $result_position->fetch_assoc();
  ?>
  <table>
    <caption>
    <?php echo $year.'年'.$array_position['position_name']; ?>编制
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="12%">部门</th>
      <?php
      for($i=1;$i<=12;$i++){
		  echo "<th>".$i."月</th>";
	  }
	  ?>
    </tr>
    <?php foreach($array_dept as $deptid=>$dept_name){ ?>
    <tr>
      <td><?php echo $deptid; ?></td>
      <td><?php echo $dept_name; ?></td>
      <?php
      for($i=1;$i<=12;$i++){
		  $month_staffing_key = $deptid.'-'.date('Y-m',strtotime($year.'-'.$i)).'-01';
		  $month_staffing = array_key_exists($month_staffing_key,$array_month_staffing)?$array_month_staffing[$month_staffing_key]:0;
		  echo "<td>".$month_staffing."</td>";
	  }
	  ?>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="2">Total</td>
      <?php
      for($i=1;$i<=12;$i++){
		  $total_staffing_key = date('Y-m',strtotime($year.'-'.$i)).'-01';
		  $total_staffing = array_key_exists($total_staffing_key,$array_total_staffing)?$array_total_staffing[$total_staffing_key]:0;
		  echo "<td>".$total_staffing."</td>";
	  }
	  ?>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>