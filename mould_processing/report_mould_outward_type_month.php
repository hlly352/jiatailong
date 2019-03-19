<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
//outward_typeid+month
$sql_outward_type_month = "SELECT `outward_typeid`,DATE_FORMAT(`order_date`,'%Y-%m') AS `month`,SUM(`cost`) AS `cost` FROM `db_mould_outward` WHERE DATE_FORMAT(`order_date`,'%Y') = '$year' AND `outward_status` = 1 GROUP BY `outward_typeid`,DATE_FORMAT(`order_date`,'%Y-%m')";
$result_outward_type_month = $db->query($sql_outward_type_month);
if($result_outward_type_month->num_rows){
	while($row_outward_type_month = $result_outward_type_month->fetch_assoc()){
		$array_outward_type_month[$row_outward_type_month['outward_typeid'].'-'.$row_outward_type_month['month']] = $row_outward_type_month['cost'];
		$array_outward_type[] = $row_outward_type_month['outward_typeid'];
	}
}else{
	$array_outward_type_month = array();
	$array_outward_type = array();
}
//month
$sql_month = "SELECT DATE_FORMAT(`order_date`,'%Y-%m') AS `month`,SUM(`cost`) AS `cost` FROM `db_mould_outward` WHERE DATE_FORMAT(`order_date`,'%Y') = '$year' AND `outward_status` = 1 GROUP BY DATE_FORMAT(`order_date`,'%Y-%m')";
$result_month = $db->query($sql_month);
if($result_month->num_rows){
	while($row_month = $result_month->fetch_assoc()){
		$array_month[$row_month['month']] = $row_month['cost'];
	}
}else{
	$array_month = array();
}
$array_outward_type = array_values(array_unique($array_outward_type));
$array_outward_typeid = fun_convert_checkbox($array_outward_type);
$sql_outward_type = "SELECT `outward_typeid`,`outward_typename` FROM `db_mould_outward_type` WHERE `outward_typeid` IN ($array_outward_typeid) ORDER BY `outward_typeid` ASC";
$result_outward_type = $db->query($sql_outward_type);
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
  <h4>外协类型月报表</h4>
  <form action="" name="report_mould_outward_type_month" method="get">
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
  <?php if($result_outward_type->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="12%">外发类型</th>
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
    while($row_outward_type = $result_outward_type->fetch_assoc()){
		$outward_typeid = $row_outward_type['outward_typeid'];
	?>
    <tr>
      <td><?php echo $a; ?></td>
      <td><?php echo $row_outward_type['outward_typename']; ?></td>
      <?php
	  $total_cost = 0;
      for($i=1;$i<=12;$i++){
		  $month = date('Y-m',strtotime($year.'-'.$i));
		  $month_key = $outward_typeid.'-'.$month;
		  $outward_type_month_cost = array_key_exists($month_key,$array_outward_type_month)?$array_outward_type_month[$month_key]:0;
		  echo "<td>".$outward_type_month_cost."</td>";
		  $total_cost += $outward_type_month_cost;
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