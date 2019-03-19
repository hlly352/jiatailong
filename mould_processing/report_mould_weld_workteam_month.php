<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
//workteamid+month
$sql_workteam_month = "SELECT `workteamid`,DATE_FORMAT(`order_date`,'%Y-%m') AS `month`,SUM(`cost`) AS `cost` FROM `db_mould_weld` WHERE DATE_FORMAT(`order_date`,'%Y') = '$year' AND `weld_status` = 1 GROUP BY `workteamid`,DATE_FORMAT(`order_date`,'%Y-%m')";
$result_workteam_month = $db->query($sql_workteam_month);
if($result_workteam_month->num_rows){
	while($row_workteam_month = $result_workteam_month->fetch_assoc()){
		$array_workteam_month[$row_workteam_month['workteamid'].'-'.$row_workteam_month['month']] = $row_workteam_month['cost'];
		$array_workteam[] = $row_workteam_month['workteamid'];
	}
}else{
	$array_workteam_month = array();
	$array_workteam = array();
}
//month
$sql_month = "SELECT DATE_FORMAT(`order_date`,'%Y-%m') AS `month`,SUM(`cost`) AS `cost` FROM `db_mould_weld` WHERE DATE_FORMAT(`order_date`,'%Y') = '$year' AND `weld_status` = 1 GROUP BY DATE_FORMAT(`order_date`,'%Y-%m')";
$result_month = $db->query($sql_month);
if($result_month->num_rows){
	while($row_month = $result_month->fetch_assoc()){
		$array_month[$row_month['month']] = $row_month['cost'];
	}
}else{
	$array_month = array();
}
$array_workteam = array_values(array_unique($array_workteam));
$array_workteamid = fun_convert_checkbox($array_workteam);
$sql_workteam = "SELECT `workteamid`,`workteam_name` FROM `db_mould_workteam` WHERE `workteamid` IN ($array_workteamid) ORDER BY `workteamid` ASC";
$result_workteam = $db->query($sql_workteam);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>烧焊组别月报表</h4>
  <form action="" name="report_mould_weld_workteam_month" method="get">
    <table>
      <tr>
        <th>年份：</th>
        <td><input type="text" name="year" value="<?php echo $year; ?>" onfocus="WdatePicker({dateFmt:'yyyy',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result_workteam->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="12%">组别</th>
      <?php
      for($i=1;$i<=12;$i++){
		  $month = date('Y-m',strtotime($year.'-'.$i));
		  echo "<th width=\"6.5%\">".$month."</th>";
	  }
	  ?>
      <th width="6%">Total</th>
    </tr>
    <?php
	$a = 1;
    while($row_workteam = $result_workteam->fetch_assoc()){
		$workteamid = $row_workteam['workteamid'];
	?>
    <tr>
      <td><?php echo $a; ?></td>
      <td><?php echo $row_workteam['workteam_name']; ?></td>
      <?php
	  $total_cost = 0;
      for($i=1;$i<=12;$i++){
		  $month = date('Y-m',strtotime($year.'-'.$i));
		  $month_key = $workteamid.'-'.$month;
		  $workteam_month_cost = array_key_exists($month_key,$array_workteam_month)?$array_workteam_month[$month_key]:0;
		  echo "<td>".$workteam_month_cost."</td>";
		  $total_cost += $workteam_month_cost;
	  }
	  ?>
      <td><?php echo $total_cost; ?></td>
    <tr>
    <?php
    $a++;
	}
	?>
    <tr>
      <td colspan="2">Total</td>
      <?php
      for($i=1;$i<=12;$i++){
		  $month = date('Y-m',strtotime($year.'-'.$i));
		  $month_cost = array_key_exists($month,$array_month)?$array_month[$month]:0;
		  echo "<td>".$month_cost."</td>";
	  }
	  ?>
      <td><?php echo array_sum($array_month); ?></td>
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