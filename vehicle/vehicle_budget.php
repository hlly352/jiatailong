<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$year = $_GET['year']?$_GET['year']:date('Y');
$sql_budget = "SELECT `budget_cost`,DATE_FORMAT(`budget_month`,'%Y-%m') AS `budget_month` FROM `db_vehicle_budget` WHERE DATE_FORMAT(`budget_month`,'%Y') = '$year'";
$result_budget = $db->query($sql_budget);
if($result_budget->num_rows){
	while($row_budge = $result_budget->fetch_assoc()){
		$array_budget[$row_budge['budget_month']] = $row_budge['budget_cost'];
	}
}else{
	$array_budget = array();
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
  <h4>费用预算</h4>
  <form action="" name="action" method="get">
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
      <th width="20%">月份</th>
      <th>预算费用(元)</th>
      <th width="4%">Edit</th>
    </tr>
    <?php
    for($i=1;$i<=12;$i++){
		$month = date('Y-m',strtotime($year.'-'.$i));
		$amount = array_key_exists($month,$array_budget)?$array_budget[$month]:0;
	?>
    <tr>
      <td><?php echo $month; ?></td>
      <td><?php echo $amount; ?></td>
      <td><a href="vehicle_budgetae.php?month=<?php echo $month; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
    </tr>
    <?php } ?>
  </table>
</div>
<?php include "../footer.php"; ?>
</body>
</html>