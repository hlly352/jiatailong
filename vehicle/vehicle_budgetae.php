<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var budget_cost = $("#budget_cost").val();
		if(!ri_a.test(budget_cost)){
			$("#budget_cost").focus();
			return false;
		}
	})
})
</script>
<title>用车管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
$month = trim($_GET['month']);
$budget_month = $month.'-01';
$sql_budget = "SELECT `budget_cost` FROM `db_vehicle_budget` WHERE `budget_month` = '$budget_month'";
$result_budget = $db->query($sql_budget);
$array_budget = $result_budget->fetch_assoc();
$budget_cost = ($result_budget->num_rows)?$array_budget['budget_cost']:0;
?>
<div id="table_sheet">
  <h4>预算费用修改</h4>
  <form action="vehicle_budgetdo.php" name="vehicle_budget" method="post">
    <table>
      <tr>
        <th width="20%">月份：</th>
        <td width="80%"><input type="text" name="budget_month" value="<?php echo $month; ?>" class="input_txt" readonly="readonly" /></td>
      </tr>
      <tr>
        <th>费用：</th>
        <td><input type="text" name="budget_cost" id="budget_cost" value="<?php echo $budget_cost; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" /></td>
      </tr>
    </table>
  </form>
</div>
<?php include "../footer.php"; ?>
</body>
</html>