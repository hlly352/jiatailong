<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$month = $_GET['month']?$_GET['month']:date('Y-m');
$month_days = date('t',strtotime($month."-01"));
$sql = "SELECT DATE_FORMAT(`dotime`,'%Y-%m-%d') AS `date`,COUNT(*) AS `count` FROM `db_login_log` WHERE `login_status` = 'A' AND DATE_FORMAT(`dotime`,'%Y-%m') = '$month' GROUP BY DATE_FORMAT(`dotime`,'%Y-%m-%d')";
$result = $db->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		$array_data[$row['date']] = $row['count'];
	}
}else{
	$array_data = array();
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>系统设置-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>登录日报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>月份：</th>
        <td><input type="text" name="month" value="<?php echo $month; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
        <input type="button" name="submit" value="图示" class="button" onclick="window.open('jpgraph_report_login_day.php?month='+search.month.value+'&submit='+search.submit.value)" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <table>
    <tr>
      <th width="50%">日期</th>
      <th width="50%">次数</th>
    </tr>
    <?php
    for($i=1;$i<=$month_days;$i++){
		$str_date = strtotime($month.'-'.$i);
		$date = date('Y-m-d',$str_date);
		$week = date('w',$str_date);
		$day = ($week == 6 || $week == 0)?'<font color=red>'.$date.'</font>':$date;
		$count = array_key_exists($date,$array_data)?$array_data[$date]:0;
	?>
    <tr>
      <td><?php echo $day; ?></td>
      <td><?php echo $count; ?></td>
    </tr>
    <?php } ?>
    <tr>
      <td>Total</td>
      <td><?php echo array_sum($array_data); ?></td>
    </tr>
  </table>
</div>
<?php include "../footer.php"; ?>
</body>
</html>