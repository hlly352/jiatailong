<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate ." +1 month -1 day"));
$sql = "SELECT `db_vehicle_list`.`vehicleid`,SUM((`db_vehicle_list`.`odometer_finish`-`db_vehicle_list`.`odometer_start`)*`charge`+(ROUND(`db_vehicle_list`.`wait_time`)*`db_vehicle_list`.`charge_wait`)+`db_vehicle_list`.`charge_toll`+`db_vehicle_list`.`charge_parking`) AS `cost`,`db_vehicle`.`plate_number`,`db_vehicle`.`owner` FROM `db_vehicle_list` INNER JOIN `db_vehicle` ON `db_vehicle`.`vehicleid` = `db_vehicle_list`.`vehicleid` WHERE `db_vehicle_list`.`vehicle_status` = 1 AND `db_vehicle_list`.`approve_status` = 'B' AND `db_vehicle_list`.`reckoner` != 0 AND (`db_vehicle_list`.`apply_date` BETWEEN '$sdate' AND '$edate') GROUP BY `db_vehicle_list`.`vehicleid`";
$result = $db->query($sql);
$_SESSION['report_vehicle_month_settle'] = $sql;
$result_allid = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_vehicle_list`.`vehicleid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
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
  <h4>月结报表</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>申请日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>--</th>
        <td><input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_report_vehicle_month_settle.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_allid = $result_allid->fetch_assoc()){
		  $array_listid .= $row_allid['vehicleid'].',';
	  }
	  $array_listid = rtrim($array_listid,',');
	  $sql_cost = "SELECT SUM((`odometer_finish`-`odometer_start`)*`charge`+(ROUND(`wait_time`)*`charge_wait`)+`charge_toll`+`charge_parking`) AS `cost` FROM `db_vehicle_list` WHERE `vehicle_status` = 1 AND `approve_status` = 'B' AND (`apply_date` BETWEEN '$sdate' AND '$edate') AND `vehicleid` IN ($array_listid)";
	  $result_cost = $db->query($sql_cost);
	  $array_cost = $result_cost->fetch_assoc();
	  $all_cost = $array_cost['cost'];
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="32%">车辆车牌</th>
      <th width="32%">联系人</th>
      <th width="32%">总费用(元)</th>
    </tr>
    <?php while($row = $result->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row['vehicleid']; ?></td>
      <td><?php echo $row['plate_number']; ?></td>
      <td><?php echo $row['owner']; ?></td>
      <td><?php echo $row['cost']; ?></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="3">Total</td>
      <td><?php echo number_format($all_cost,1); ?></td>
    </tr>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>