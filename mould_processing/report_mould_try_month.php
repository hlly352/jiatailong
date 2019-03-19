<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
$sql = "SELECT DATE_FORMAT(`try_date`,'%Y-%m') AS `month`,SUM(`cost`) AS `cost` FROM `db_mould_try` WHERE DATE_FORMAT(`try_date`,'%Y') = '$year' AND `try_status` = 1 GROUP BY DATE_FORMAT(`try_date`,'%Y-%m')";
$result = $db->query($sql);
if($result->num_rows){
	while($row = $result->fetch_assoc()){
		$array_cost[$row['month']] = $row['cost'];
	}
}else{
	$array_cost = array();
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>试模费用月报表</h4>
  <form action="" name="report_mould_try_month" method="get">
    <table>
      <tr>
        <th>年份：</th>
        <td><input type="text" name="year" value="<?php echo $year; ?>" onfocus="WdatePicker({dateFmt:'yyyy',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="图示" class="button" onclick="window.open('jpgraph_report_mould_try_month.php?year=<?php echo $year; ?>')" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <table>
    <tr>
      <th width="50%">月份</th>
      <th width="50%">金额</th>
    </tr>
    <?php
    for($i=1;$i<=12;$i++){
		$month = date('Y-m',strtotime($year.'-'.$i));
		$month_cost = array_key_exists($month,$array_cost)?$array_cost[$month]:0;
	?>
    <tr>
      <td><?php echo $month; ?></td>
      <td><?php echo $month_cost; ?></td>
    </tr>
    <?php } ?>
    <tr>
      <td>Total</td>
      <td><?php echo array_sum($array_cost); ?></td>
    </tr>
  </table>
</div>
<?php include "../footer.php"; ?>
</body>
</html>