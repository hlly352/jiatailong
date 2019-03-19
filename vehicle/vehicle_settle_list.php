<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate ." +1 month -1 day"));
$sql_vehicle = "SELECT `vehicleid`,`plate_number`,`owner` FROM `db_vehicle` ORDER BY `vehicleid` DESC";
$result_vehicle = $db->query($sql_vehicle);
if($_GET['submit']){
	$vehicle_num = trim($_GET['vehicle_num']);
	$vehicleid  = $_GET['vehicleid'];
	if($vehicleid){
		$sqlvehicleid = " AND `db_vehicle_list`.`vehicleid` = '$vehicleid'";
	}
	$sqlwhere = " AND `db_vehicle_list`.`vehicle_num` LIKE '%$vehicle_num%' $sqlvehicleid";
}
//统计月份
$sql = "SELECT `db_vehicle_list`.`listid`,`db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`apply_date`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`passby`,`db_vehicle_list`.`odometer_start`,`db_vehicle_list`.`odometer_finish`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`pathtype`,`db_vehicle_list`.`charge`,`db_vehicle_list`.`charge_wait`,`db_vehicle_list`.`wait_time`,`db_vehicle_list`.`charge_parking`,`db_vehicle_list`.`charge_toll`,(`db_vehicle_list`.`odometer_finish`-`db_vehicle_list`.`odometer_start`) AS `kilometres`,((`db_vehicle_list`.`odometer_finish`-`db_vehicle_list`.`odometer_start`)*`charge`+(ROUND(`db_vehicle_list`.`wait_time`)*`db_vehicle_list`.`charge_wait`)+`db_vehicle_list`.`charge_toll`+`db_vehicle_list`.`charge_parking`) AS `cost`,`db_vehicle`.`plate_number`,`db_vehicle`.`owner` FROM `db_vehicle_list` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_list`.`deptid` INNER JOIN `db_vehicle` ON `db_vehicle`.`vehicleid` = `db_vehicle_list`.`vehicleid` WHERE `db_vehicle_list`.`vehicle_status` = 1 AND `db_vehicle_list`.`approve_status` = 'B' AND `db_vehicle_list`.`reckoner` != 0 AND (`db_vehicle_list`.`apply_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$_SESSION['vehicle_settle_list'] = $sql;
$result_allid = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_vehicle_list`.`listid` DESC" . $pages->limitsql;
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
  <h4>结算清单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>派车单号：</th>
        <td><input type="text" name="vehicle_num" class="input_txt" /></td>
        <th>车辆车牌：</th>
        <td><select name="vehicleid">
            <option value="">所有</option>
            <?php
            if($result_vehicle->num_rows){
				while($row_vehicle = $result_vehicle->fetch_assoc()){
					echo "<option value=\"".$row_vehicle['vehicleid']."\">".$row_vehicle['plate_number'].'('.$row_vehicle['owner'].')'."</option>";
				}
			}
			?>
          </select></td>
        <th>申请日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>--</th>
        <td><input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_vehicle_settle_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_allid = $result_allid->fetch_assoc()){
		  $array_listid .= $row_allid['listid'].',';
	  }
	  $array_listid = rtrim($array_listid,',');
	  $sql_cost = "SELECT SUM((`odometer_finish`-`odometer_start`)*`charge`+(ROUND(`wait_time`)*`charge_wait`)+`charge_toll`+`charge_parking`) AS `total_cost` FROM `db_vehicle_list` WHERE `vehicle_status` = 1 AND `approve_status` = 'B' AND `db_vehicle_list`.`reckoner` != 0 AND (`apply_date` BETWEEN '$sdate' AND '$edate') AND `listid` IN ($array_listid)";
	  $result_cost = $db->query($sql_cost);
	  $array_cost = $result_cost->fetch_assoc();
	  $all_cost = $array_cost['total_cost'];
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="6%">派车单号</th>
      <th width="6%">车辆车牌</th>
      <th width="5%">联系人</th>
      <th width="5%">日期</th>
      <th width="5%">里程类型</th>
      <th width="5%">路程方式</th>
      <th width="6%">出发地</th>
      <th width="7%">目的地</th>
      <th width="6%">途径地</th>
      <th width="5%">出厂码表<br />
        里程(Km)</th>
      <th width="5%">进厂码表<br />
        里程(Km)</th>
      <th width="5%">里程数<br />
        (Km)</th>
      <th width="5%">计费单价<br />
        (元/公里)</th>
      <th width="5%">等候单价<br />
        (元/公里)</th>
      <th width="5%">等待时间<br />
        (H)</th>
      <th width="5%">停车费<br />
        (元)</th>
      <th width="5%">过路费<br />
        (元)</th>
      <th width="5%">总费用<br />
        (元)</th>
    </tr>
    <?php while($row = $result->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row['listid']; ?></td>
      <td><?php echo $row['vehicle_num']; ?></td>
      <td><?php echo $row['plate_number']; ?></td>
      <td><?php echo $row['owner']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $array_vehicle_pathtype[$row['pathtype']]; ?></td>
      <td><?php echo $array_vehicle_roundtype[$row['roundtype']]; ?></td>
      <td><?php echo $row['departure']; ?></td>
      <td><?php echo $row['destination']; ?></td>
      <td><?php echo $row['passby']; ?></td>
      <td><?php echo $row['odometer_start']; ?></td>
      <td><?php echo $row['odometer_finish']; ?></td>
      <td><?php echo $row['kilometres']; ?></td>
      <td><?php echo $row['charge']; ?></td>
      <td><?php echo $row['charge_wait']; ?></td>
      <td><?php echo $row['wait_time']; ?></td>
      <td><?php echo $row['charge_parking']; ?></td>
      <td><?php echo $row['charge_toll']; ?></td>
      <td><?php echo $row['cost']; ?></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="18">Total</td>
      <td><?php echo $all_cost; ?></td>
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