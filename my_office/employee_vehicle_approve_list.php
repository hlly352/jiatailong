<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
	$vehicle_num = trim($_GET['vehicle_num']);
	$applyer_name = trim($_GET['applyer_name']);
	$sqlwhere = " AND `db_vehicle_list`.`vehicle_num` LIKE '%$vehicle_num%' AND `db_employee`.`employee_name` LIKE '%$applyer_name%'";
}
$sql = "SELECT `db_vehicle_list`.`listid`,`db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`start_time`,`db_vehicle_list`.`finish_time`,`db_vehicle_list`.`dotype`,`db_vehicle_list`.`vehicle_category`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`apply_date`,`db_employee`.`employee_name` FROM `db_vehicle_list` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_vehicle_list`.`applyer` INNER JOIN `db_vehicle_flow` ON `db_vehicle_flow`.`flowid` = `db_vehicle_list`.`flowid` WHERE (`db_vehicle_flow`.`approver` = '$employeeid' OR `db_vehicle_flow`.`certigier` = '$employeeid') AND `db_vehicle_list`.`vehicle_status` = 1 AND `db_vehicle_list`.`approve_status` = 'A' $sqlwhere";
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>我的办公-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>我的待审批用车</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>派车单号：</th>
        <td><input type="text" name="vehicle_num" class="input_txt" /></td>
        <th>申请人：</th>
        <td><input type="text" name="applyer_name" class="input_txt" /></td>
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
      <th width="8%">申请人</th>
      <th width="8%">申请日期</th>
      <th width="6%">用车类型</th>
      <th width="6%">车辆类型</th>
      <th width="12%">出发地</th>
      <th width="18%">目的地</th>
      <th width="6%">路程方式</th>
      <th width="10%">预计出厂时间</th>
      <th width="10%">预计返厂时间</th>
      <th width="4%">审批</th>
    </tr>
    <?php while($row = $result->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row['listid']; ?></td>
      <td><?php echo $row['vehicle_num']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $array_vehicle_dotype[$row['dotype']]; ?></td>
      <td><?php echo $array_vehicle_category[$row['vehicle_category']]; ?></td>
      <td><?php echo $row['departure']; ?></td>
      <td><?php echo $row['destination']; ?></td>
      <td><?php echo $array_vehicle_roundtype[$row['roundtype']]; ?></td>
      <td><?php echo $row['start_time']; ?></td>
      <td><?php echo $row['finish_time']; ?></td>
      <td><a href="employee_vehicle_approve.php?id=<?php echo $row['listid']; ?>"><img src="../images/system_ico/approve_10_10.png" width="10" height="10" /></a></td>
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