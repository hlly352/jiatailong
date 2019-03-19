<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01',strtotime("-1 month"));
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime(date('Y-m-01')."+1 month -1 day"));
if($_GET['submit']){
	$vehicle_num = trim($_GET['vehicle_num']);
	$applyer_name = trim($_GET['applyer_name']);
	$vehicle_status = $_GET['vehicle_status'];
	if($vehicle_status != NULL){
		$sql_vehicle_status = " AND `db_vehicle_list`.`vehicle_status` = '$vehicle_status'";
	}
	$sqlwhere = " AND `db_vehicle_list`.`vehicle_num` LIKE '%$vehicle_num%' AND `db_applyer`.`employee_name` LIKE '%$applyer_name%' $sql_vehicle_status";
}else{
	$vehicle_status = 1;
	$sqlwhere = " AND `db_vehicle_list`.`vehicle_status` = '$vehicle_status'";
}
$sql = "SELECT `db_vehicle_list`.`listid`,`db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`apply_date`,(`db_vehicle_list`.`odometer_finish`-`db_vehicle_list`.`odometer_start`) AS `kilometres`,`db_vehicle_list`.`wait_time`,`db_vehicle_list`.`charge_parking`,`db_vehicle_list`.`charge_toll`,`db_vehicle_list`.`reckoner`,`db_vehicle_list`.`vehicle_status`,CONCAT(`db_vehicle`.`plate_number`,'(',`db_vehicle`.`owner`,')') AS `vehicle`,`db_department`.`dept_name`,`db_applyer`.`employee_name` AS `applyer_name`,`db_reckoner`.`employee_name` AS `reckoner_name` FROM `db_vehicle_list` INNER JOIN `db_vehicle` ON `db_vehicle`.`vehicleid` = `db_vehicle_list`.`vehicleid` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_list`.`deptid` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_vehicle_list`.`applyer` LEFT JOIN `db_employee` AS `db_reckoner` ON `db_reckoner`.`employeeid` = `db_vehicle_list`.`reckoner` WHERE `db_vehicle_list`.`approve_status` = 'B' AND (`db_vehicle_list`.`apply_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
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
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>门卫管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>用车</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>派车单号：</th>
        <td><input type="text" name="vehicle_num" class="input_txt" /></td>
        <th>申请人：</th>
        <td><input type="text" name="applyer_name" class="input_txt" /></td>
        <th>申请日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>状态：</th>
        <td><select name="vehicle_status">
            <option value="">所有</option>
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $vehicle_status && $vehicle_status != NULL) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">派车单号</th>
      <th width="8%">车辆</th>
      <th width="6%">申请人</th>
      <th width="8%">申请日期</th>
      <th width="8%">出发地</th>
      <th width="10%">目的地</th>
      <th width="6%">路程方式</th>
      <th width="6%">公里数(Km)</th>
      <th width="6%">等候时间(H)</th>
      <th width="6%">停车费(元)</th>
      <th width="6%">过路费(元)</th>
      <th width="6%">结算人</th>
      <th width="4%">状态</th>
      <th width="4%">Settle</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$reckoner = $row['reckoner'];
		$reckoner_name = $row['reckoner']?$row['reckoner_name']:'--';
	?>
    <tr>
      <td><?php echo $row['listid']; ?></td>
      <td><?php echo $row['vehicle_num']; ?></td>
      <td><?php echo $row['vehicle']; ?></td>
      <td><?php echo $row['applyer_name']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $row['departure']; ?></td>
      <td><?php echo $row['destination']; ?></td>
      <td><?php echo $array_vehicle_roundtype[$row['roundtype']]; ?></td>
      <td><?php echo $row['kilometres']; ?></td>
      <td><?php echo $row['wait_time']; ?></td>
      <td><?php echo $row['charge_parking']; ?></td>
      <td><?php echo $row['charge_toll']; ?></td>
      <td><?php echo $reckoner_name; ?></td>
      <td><?php echo $array_status[$row['vehicle_status']]; ?></td>
      <td><?php if($reckoner == 0 || $reckoner == $employeeid){ ?><a href="employee_vehicle_settle.php?id=<?php echo $row['listid']; ?>"><img src="../images/system_ico/settle_10_10.png" width="10" height="10" /></a><?php } ?></td>
      <td><a href="employee_vehicle_info.php?id=<?php echo $row['listid']; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
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