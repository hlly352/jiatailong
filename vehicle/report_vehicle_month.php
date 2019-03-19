<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
//统计预算费用
$sql_budget = "SELECT `budget_cost`,DATE_FORMAT(`budget_month`,'%Y-%m') AS `budget_month` FROM `db_vehicle_budget` WHERE DATE_FORMAT(`budget_month`,'%Y') = '$year'";
$result_budget = $db->query($sql_budget);
if($result_budget->num_rows){
	while($row_budget = $result_budget->fetch_assoc()){
		$array_budget[$row_budget['budget_month']] = $row_budget['budget_cost'];
	}
}else{
	$array_budget = array();
}
//统计实际费用
$sql_vehicle = "SELECT DATE_FORMAT(`apply_date`,'%Y-%m') AS `apply_month`,COUNT(*) AS count,SUM((`odometer_finish`-`odometer_start`)*`charge`+(ROUND(`wait_time`)*`charge_wait`)+`charge_toll`+`charge_parking`) AS `cost` FROM `db_vehicle_list` WHERE `vehicle_status` = 1 AND `approve_status` = 'B' AND `reckoner` != 0 AND DATE_FORMAT(`apply_date`,'%Y') = '$year' GROUP BY DATE_FORMAT(`apply_date`,'%Y-%m')";
$result_vehicle = $db->query($sql_vehicle);
if($result_vehicle->num_rows){
	while($row_vehicle = $result_vehicle->fetch_assoc()){
		$array_vehicle[$row_vehicle['apply_month']] = array('cost'=>$row_vehicle['cost'],'count'=>$row_vehicle['count']);
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
  <h4>用车月报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>年份：
          <input type="text" name="year" value="<?php echo $year; ?>" onfocus="WdatePicker({dateFmt:'yyyy',isShowClear:false,readOnly:true})" class="input_txt" /></th>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="submit" value="图示" class="button" onclick="window.open('jpgraph_report_employee_vehicle_month.php?year='+search.year.value+'&submit='+search.submit.value)" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <table>
    <tr>
      <th width="20%">月份</th>
      <th width="27%">实际费用(元)</th>
      <th width="27%">预算费用(元)</th>
      <th width="26%">次数</th>
    </tr>
    <?php
    for($i=1;$i<=12;$i++){
		$str_time = strtotime($year.'-'.$i);
		$month = date('Y-m',$str_time);
		$cost = array_key_exists($month,$array_vehicle)?$array_vehicle[$month]['cost']:'0';
		$budget_cost = array_key_exists($month,$array_budget)?$array_budget[$month]:'0';
		$count = array_key_exists($month,$array_vehicle)?$array_vehicle[$month]['count']:'0';
	?>
    <tr>
      <td><?php echo $month; ?></td>
      <td><?php echo $cost; ?></td>
      <td><?php echo $budget_cost; ?></td>
      <td><?php echo $count; ?></td>
    </tr>
    <?php
	$all_cost += $cost;
	$all_budget_cost += $budget_cost;
	$all_count += $count;
	}
	?>
    <tr>
      <td>&nbsp;</td>
      <td>Total:<?php echo number_format($all_cost,1); ?></td>
      <td>Total:<?php echo number_format($all_budget_cost,1); ?></td>
      <td>Total:<?php echo $all_count; ?></td>
    </tr>
  </table>
</div>
<?php include "../footer.php"; ?>
</body>
</html>