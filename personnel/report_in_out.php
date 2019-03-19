<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
$sql_in = "SELECT DATE_FORMAT(`entrydate`,'%Y-%m') AS `month`,COUNT(*) AS `count` FROM `db_employee` WHERE DATE_FORMAT(`entrydate`,'%Y') = '$year' GROUP BY DATE_FORMAT(`db_employee`.`entrydate`,'%Y-%m')";
$result_in = $db->query($sql_in);
if($result_in->num_rows){
	while($row_in = $result_in->fetch_assoc()){
		$array_in[$row_in['month']] = $row_in['count'];
	}
}else{
	$array_in = array();
}
$sql_out = "SELECT DATE_FORMAT(`termdate`,'%Y-%m') AS `month`,COUNT(*) AS `count` FROM `db_employee` WHERE  DATE_FORMAT(`termdate`,'%Y') = '$year' AND `employee_status` = 0 GROUP BY DATE_FORMAT(`termdate`,'%Y-%m')";
$result_out = $db->query($sql_out);
if($result_out->num_rows){
	while($row_out = $result_out->fetch_assoc()){
		$array_out[$row_out['month']] = $row_out['count'];
	}
}else{
	$array_out = array();
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
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>入/离月报表</h4>
  <form action="" name="search" method="get">
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
  <table>
    <tr>
      <th width="12%">月份</th>
      <th width="44%">入职人数</th>
      <th width="44%">离职人数</th>
    </tr>
    <?php
    for($i=1;$i<=12;$i++){
		$month = date('Y-m',strtotime($year.'-'.$i));
		$in_count = (array_key_exists($month,$array_in))?$array_in[$month]:0;
		$out_count = (array_key_exists($month,$array_out))?$array_out[$month]:0;
	?>
    <tr>
      <td><?php echo $month; ?></td>
      <td><?php echo $in_count; ?></td>
      <td><?php echo $out_count; ?></td>
    </tr>
    <?php
	$in_sum_count += $in_count;
	$out_sum_count += $out_count;
	}
    ?>
    <tr>
      <td>Total</td>
      <td><?php echo $in_sum_count; ?></td>
      <td><?php echo $out_sum_count; ?></td>
    </tr>
  </table>
</div>
<?php include "../footer.php"; ?>
</body>
</html>