<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
//try_causeid+month
$sql_try_cause_month = "SELECT `try_causeid`,DATE_FORMAT(`try_date`,'%Y-%m') AS `month`,SUM(`cost`) AS `cost` FROM `db_mould_try` WHERE DATE_FORMAT(`try_date`,'%Y') = '$year' AND `try_status` = 1 GROUP BY `try_causeid`,DATE_FORMAT(`try_date`,'%Y-%m')";
$result_try_cause_month = $db->query($sql_try_cause_month);
if($result_try_cause_month->num_rows){
	while($row_try_cause_month = $result_try_cause_month->fetch_assoc()){
		$array_try_cause_month[$row_try_cause_month['try_causeid'].'-'.$row_try_cause_month['month']] = $row_try_cause_month['cost'];
		$array_try_cause[] = $row_try_cause_month['try_causeid'];
	}
}else{
	$array_try_cause_month = array();
	$array_try_cause = array();
}
//month
$sql_month = "SELECT DATE_FORMAT(`try_date`,'%Y-%m') AS `month`,SUM(`cost`) AS `cost` FROM `db_mould_try` WHERE DATE_FORMAT(`try_date`,'%Y') = '$year' AND `try_status` = 1 GROUP BY DATE_FORMAT(`try_date`,'%Y-%m')";
$result_month = $db->query($sql_month);
if($result_month->num_rows){
	while($row_month = $result_month->fetch_assoc()){
		$array_month[$row_month['month']] = $row_month['cost'];
	}
}else{
	$array_month = array();
}
$array_try_cause = array_values(array_unique($array_try_cause));
$array_try_causeid = fun_convert_checkbox($array_try_cause);
$sql_try_cause = "SELECT `try_causeid`,`try_causename` FROM `db_mould_try_cause` WHERE `try_causeid` IN ($array_try_causeid) ORDER BY `try_causeid` ASC";
$result_try_cause = $db->query($sql_try_cause);
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
  <h4>试模原因月报表</h4>
  <form action="" name="report_try_cause_try_month" method="get">
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
  <?php if($result_try_cause->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="12%">试模原因</th>
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
    while($row_try_cause = $result_try_cause->fetch_assoc()){
		$try_causeid = $row_try_cause['try_causeid'];
	?>
    <tr>
      <td><?php echo $a; ?></td>
      <td><?php echo $row_try_cause['try_causename']; ?></td>
      <?php
	  $total_cost = 0;
      for($i=1;$i<=12;$i++){
		  $month = date('Y-m',strtotime($year.'-'.$i));
		  $month_key = $try_causeid.'-'.$month;
		  $try_cause_month_cost = array_key_exists($month_key,$array_try_cause_month)?$array_try_cause_month[$month_key]:0;
		  echo "<td>".$try_cause_month_cost."</td>";
		  $total_cost += $try_cause_month_cost;
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