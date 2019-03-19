<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
if($_GET['submit']){
	$overtime_num = trim($_GET['overtime_num']);
	$applyer_name = trim($_GET['applyer_name']);
	$sqlwhere = " AND `db_employee_overtime`.`overtime_num` LIKE '%$overtime_num%' AND `db_applyer`.`employee_name` LIKE '%$applyer_name%'";
}
$sql = "SELECT `db_employee_overtime`.`overtimeid`,`db_employee_overtime`.`overtime_num`,`db_employee_overtime`.`confirmer`,`db_employee_overtime`.`apply_date`,DATE_FORMAT(`db_employee_overtime`.`start_time`,'%m-%d %H:%i') AS `start_time`,DATE_FORMAT(`db_employee_overtime`.`finish_time`,'%m-%d %H:%i') AS `finish_time`,`db_employee_overtime`.`overtime`,`db_employee_overtime`.`overtime_cause`,`db_employee_overtime`.`approve_status`,`db_employee_overtime`.`overtime_status`,`db_applyer`.`employee_name` AS `applyer_name`,`db_confirmer`.`employee_name` AS `confirmer_name`,`db_department`.`dept_name` FROM `db_employee_overtime` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_overtime`.`applyer` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_applyer`.`deptid` LEFT JOIN `db_employee` AS `db_confirmer` ON `db_confirmer`.`employeeid` = `db_employee_overtime`.`confirmer` WHERE (`db_employee_overtime`.`apply_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_employee_overtime`.`overtimeid` DESC" . $pages->limitsql;
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
  <h4>加班单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>加班单号：</th>
        <td><input type="text" name="overtime_num" class="input_txt" /></td>
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
      <th width="6%">加班单号</th>
      <th width="6%">部门</th>
      <th width="6%">申请人</th>
      <th width="6%">申请日期</th>
      <th width="8%">开始时间</th>
      <th width="8%">结束时间</th>
      <th width="6%">小时(H)</th>
      <th width="32%">事由</th>
      <th width="6%">确认人</th>
      <th width="4%">审批</th>
      <th width="4%">状态</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$overtimeid = $row['overtimeid'];
		$confirmer_name = $row['confirmer']?$row['confirmer_name']:'--';
	?>
    <tr>
      <td><?php echo $overtimeid; ?></td>
      <td><?php echo $row['overtime_num']; ?></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><?php echo $row['applyer_name']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $row['start_time']; ?></td>
      <td><?php echo $row['finish_time']; ?></td>
      <td><?php echo $row['overtime']; ?></td>
      <td><?php echo $row['overtime_cause'];; ?></td>
      <td><?php echo $confirmer_name; ?></td>
      <td><?php echo $array_office_approve_status[$row['approve_status']];; ?></td>
      <td><?php echo $array_status[$row['overtime_status']]; ?></td>
      <td><a href="employee_overtime_info.php?id=<?php echo $overtimeid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
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