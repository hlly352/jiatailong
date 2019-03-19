<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
$sql = "SELECT CONCAT('T',`try_times`) AS `try_times`,SUM(`cost`) AS `cost`,COUNT(*) AS `count` FROM `db_mould_try` WHERE (`try_date` BETWEEN '$sdate' AND '$edate') AND `try_status` = 1 GROUP BY `try_times` ORDER BY `try_times` ASC";
$result = $db->query($sql);
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
  <h4>试模次数报表</h4>
  <form action="" name="report_mould_try_times" method="get">
    <table>
      <tr>
        <th>试模日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="月报表" class="button" onclick="location.href='report_month_try_times_month.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="32%">试模次数</th>
      <th width="32%">金额</th>
      <th width="32%">次数</th>
    </tr>
    <?php
	$i = 1;
	$total_cost = 0;
	$total_count = 0;
    while($row = $result->fetch_assoc()){
	?>
    <tr>
      <td><?php echo $i; ?></td>
      <td><?php echo $row['try_times']; ?></td>
      <td><?php echo $row['cost']; ?></td>
      <td><?php echo $row['count']; ?></td>
    </tr>
    <?php
	$total_cost += $row['cost'];
	$total_count += $row['count'];
	$i++;
	}
	?>
    <tr>
      <td colspan="2">Total</td>
      <td><?php echo $total_cost; ?></td>
      <td><?php echo $total_count; ?></td>
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