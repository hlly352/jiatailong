<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate ." +1 month -1 day"));
if($_GET['submit']){
	$vehicle_num = trim($_GET['vehicle_num']);
	$applyer_name = trim($_GET['applyer_name']);
	$sqlwhere = " AND `db_vehicle_list`.`vehicle_num` LIKE '%$vehicle_num%' AND `db_employee`.`employee_name` LIKE '%$applyer_name%'";
}
$sql = "SELECT `db_vehicle_list`.`listid`,`db_vehicle_list`.`vehicle_num`,`db_vehicle_list`.`dotype`,`db_vehicle_list`.`vehicle_category`,`db_vehicle_list`.`departure`,`db_vehicle_list`.`destination`,`db_vehicle_list`.`roundtype`,`db_vehicle_list`.`apply_date`,`db_vehicle_list`.`pathtype`,`db_vehicle_list`.`cause`,`db_vehicle_list`.`approve_status`,`db_vehicle_list`.`vehicle_status`,`db_vehicle_list`.`vehicleid`,`db_vehicle`.`plate_number`,`db_department`.`dept_name`,`db_employee`.`employee_name` FROM `db_vehicle_list` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_vehicle_list`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_vehicle_list`.`deptid` LEFT JOIN `db_vehicle` ON `db_vehicle`.`vehicleid` = `db_vehicle_list`.`vehicleid` WHERE (`db_vehicle_list`.`apply_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>我的办公-希尔林</title>
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
      <th width="6%">派车单号</th>
      <th width="6%">部门</th>
      <th width="5%">申请人</th>
      <th width="6%">申请日期</th>
      <th width="5%">用车类型</th>
      <th width="5%">车辆类型</th>
      <th width="5%">路程方式</th>
      <th width="6%">出发地</th>
      <th width="8%">目的地</th>
      <th width="22%">事由</th>
      <th width="5%">车辆车牌</th>
      <th width="5%">里程类型</th>
      <th width="4%">审批</th>
      <th width="4%">状态</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$listid = $row['listid'];
		$plate_number = $row['vehicleid']?$row['plate_number']:'未派车';
		$pathtype = array_key_exists($row['pathtype'],$array_vehicle_pathtype)?$array_vehicle_pathtype[$row['pathtype']]:'--';
	?>
    <tr>
      <td><?php echo $listid; ?></td>
      <td><?php echo $row['vehicle_num']; ?></a></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $array_vehicle_dotype[$row['dotype']]; ?></td>
      <td><?php echo $array_vehicle_category[$row['vehicle_category']]; ?></td>
      <td><?php echo $array_vehicle_roundtype[$row['roundtype']]; ?></td>
      <td><?php echo $row['departure']; ?></td>
      <td><?php echo $row['destination']; ?></td>
      <td><?php echo $row['cause']; ?></td>
      <td><?php echo $plate_number; ?></td>
      <td><?php echo $pathtype; ?></td>
      <td><?php echo $array_office_approve_status[$row['approve_status']]; ?></td>
      <td><?php echo $array_status[$row['vehicle_status']]; ?></td>
      <td><a href="employee_vehicle_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
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