<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
$days = date('t',strtotime($month."-01"));
//读取部门
$sql_dept = "SELECT `deptid`,`dept_name` FROM `db_department` WHERE `dept_status` = 1 ORDER BY `dept_order` DESC,`deptid` ASC";
$result_dept = $db->query($sql_dept);
//统计月份
$sql_vehicle = "SELECT DATE_FORMAT(`apply_date`,'%Y-%m') AS `apply_month`,`deptid`,COUNT(*) AS count,SUM((`odometer_finish`-`odometer_start`)*`charge`+(ROUND(`wait_time`)*`charge_wait`)+`charge_toll`+`charge_parking`) AS `cost` FROM `db_vehicle_list` WHERE `vehicle_status` = 1 AND `approve_status` = 'B' AND `reckoner` != 0 AND DATE_FORMAT(`apply_date`,'%Y') = '$year' GROUP BY `deptid`,DATE_FORMAT(`apply_date`,'%Y-%m')";
$result_vehicle = $db->query($sql_vehicle);
if($result_vehicle->num_rows){
	while($row_vehicle = $result_vehicle->fetch_assoc()){
		$array_vehicle[$row_vehicle['deptid'].'-'.$row_vehicle['apply_month']] = array('cost'=>$row_vehicle['cost'],'count'=>$row_vehicle['count']);
	}
}else{
	$array_vehicle = array();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>用车管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>部门报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>年份：
          <input type="text" name="year" value="<?php echo $year; ?>" onfocus="WdatePicker({dateFmt:'yyyy',isShowClear:false,readOnly:true})" class="input_txt" /></th>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result_dept->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">部门</th>
      <?php
      for($i=1;$i<=12;$i++){
		  echo "<th>".date('Y-m',strtotime($year.'-'.$i))."</th>";
	  }?>
      <th>Total</th>
    </tr>
    <?php while($row_dept = $result_dept->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_dept['deptid']; ?></td>
      <td><?php echo $row_dept['dept_name']; ?></td>
      <?php
	  $all_cost = 0;
	  $all_count = 0;
      for($i=1;$i<=12;$i++){
		  $dept_month_key = $row_dept['deptid'].'-'.date('Y-m',strtotime($year.'-'.$i));
		  $cost = array_key_exists($dept_month_key,$array_vehicle)?$array_vehicle[$dept_month_key]['cost']:0;
		  $count = array_key_exists($dept_month_key,$array_vehicle)?$array_vehicle[$dept_month_key]['count']:0;
		  echo "<td>".$cost.'/'.$count."</td>";
		  $all_cost += $cost;
		  $all_count += $count;
	  }
	  ?>
      <td><?php echo number_format($all_cost,1).'/'.$all_count; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>