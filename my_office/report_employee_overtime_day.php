<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$month = $_GET['month']?$_GET['month']:date('Y-m');
$days = date('t',strtotime($month."-01"));
$sql = "SELECT DATE_FORMAT(`start_time`,'%Y-%m-%d') AS `start_date`,`approve_status`,COUNT(*) AS `count` FROM `db_employee_overtime` WHERE DATE_FORMAT(`start_time`,'%Y-%m') = '$month' AND `overtime_status` = 1 GROUP BY DATE_FORMAT(`start_time`,'%Y-%m-%d'),`approve_status`";
$result = $db->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		$array_overtime_day[$row['start_date'].'-'.$row['approve_status']] = $row['count'];
	}
}else{
	$array_overtime_day = array();
}
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
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>加班人数日报表(已审核)</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>月份：</th>
        <td><input type="text" name="month" value="<?php echo $month; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <table>
    <tr>
      <th width="15%">日期</th>
      <th width="25%">人数(未审核)</th>
      <th width="25%">人数(已审核)</th>
      <th width="25%">人数(合计)</th>
    </tr>
    <?php
    $all_count = 0;
    for($i=1;$i<=$days;$i++){
		$str_date = strtotime($month.'-'.$i);
		$date = date('Y-m-d',$str_date);;
		$week = date('w',$str_date);
		$day = ($week == 6 || $week == 0)?'<font color=red>'.$date.'</font>':$date;
		$key_A = $date.'-A';
		$key_B = $date.'-B';
		$count_A = array_key_exists($key_A,$array_overtime_day)?$array_overtime_day[$key_A]:'0';
		$count_B = array_key_exists($key_B,$array_overtime_day)?$array_overtime_day[$key_B]:'0';
		$count = $count_A+$count_B;
	?>
    <tr>
      <td><?php echo $day; ?></td>
      <td><?php echo $count_A; ?></td>
      <td><?php echo $count_B; ?></td>
      <td><?php echo $count; ?></td>
    </tr>
    <?php
	$all_count_A += $count_A;
	$all_count_B += $count_B;
	$all_count += $count;
	}
	?>
    <tr>
      <td>Total</td>
      <td><?php echo $all_count_A; ?></td>
      <td><?php echo $all_count_B; ?></td>
      <td><?php echo $all_count; ?></td>
    </tr>
  </table>
</div>
<?php include "../footer.php"; ?>
</body>
</html>