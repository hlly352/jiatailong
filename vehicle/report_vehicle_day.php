<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$month = $_GET['month']?$_GET['month']:date('Y-m');
$days = date('t',strtotime($month."-01"));
$sql = "SELECT `apply_date`,COUNT(*) AS count,SUM((`odometer_finish`-`odometer_start`)*`charge`+(ROUND(`wait_time`)*`charge_wait`)+`charge_toll`+`charge_parking`) AS `cost` FROM `db_vehicle_list` WHERE `vehicle_status` = 1 AND `approve_status` = 'B' AND `reckoner` != 0 AND DATE_FORMAT(`apply_date`,'%Y-%m') = '$month' GROUP BY `apply_date`";
$result = $db->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		$array_vehicle_day[$row['apply_date']] = array('cost'=>$row['cost'],'count'=>$row['count']);
	}
}else{
	$array_vehicle_day = array();
}
//print_r($array_vehicle_day);
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
  <h4>日报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>月份：
          <input type="text" name="month" value="<?php echo $month; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" class="input_txt" /></th>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="submit" value="图示" class="button" onclick="window.open('jpgraph_report_employee_vehicle_day.php?month='+search.month.value+'&submit='+search.submit.value)" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <table>
    <tr>
      <th width="20%">日期</th>
      <th width="40%">费用(元)</th>
      <th width="40%">次数</th>
    </tr>
    <?php
	$all_cost = 0;
	$all_count = 0;
    for($i=1;$i<=$days;$i++){
		$str_date = strtotime($month.'-'.$i);
		$date = date('Y-m-d',$str_date);
		$week = date('w',$str_date);
		$day = ($week == 6 || $week == 0)?'<font color=red>'.$date.'</font>':$date;
		$cost = array_key_exists($date,$array_vehicle_day)?$array_vehicle_day[$date]['cost']:'0';
		$count = array_key_exists($date,$array_vehicle_day)?$array_vehicle_day[$date]['count']:'0';
	?>
    <tr>
      <td><?php echo $day; ?></td>
      <td><?php echo $cost; ?></td>
      <td><?php echo $count; ?></td>
    </tr>
    <?php
	$all_cost += $cost;
	$all_count += $count;
	}
	?>
    <tr>
      <td>Total</td>
      <td><?php echo number_format($all_cost,1); ?></td>
      <td><?php echo $all_count; ?></td>
    </tr>
  </table>
</div>
<?php include "../footer.php"; ?>
</body>
</html>