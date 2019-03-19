<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$applyer_name = trim($_GET['applyer_name']);
	$overtime_num = trim($_GET['overtime_num']);
	$leave_num = trim($_GET['leave_num']);
	$sqlwhere = " WHERE `db_employee_leave`.`leave_num` LIKE '%$leave_num%' AND `db_employee_overtime`.`overtime_num` LIKE '%$overtime_num%' AND `db_applyer`.`employee_name` LIKE '%$applyer_name%'";
}
$sql = "SELECT `db_leave_overtime`.`lvid`,`db_leave_overtime`.`deduction_time`,`db_leave_overtime`.`dotime`,`db_employee_leave`.`leave_num`,`db_employee_leave`.`apply_date` AS `leave_applydate`,`db_employee_leave`.`leavetime`,`db_employee_overtime`.`overtime_num`,`db_employee_overtime`.`apply_date` AS `overtime_applydate`,`db_employee_overtime`.`overtime`,`db_employee`.`employee_name`,`db_applyer`.`employee_name` AS `applyer_name` FROM `db_leave_overtime` INNER JOIN `db_employee_leave` ON `db_employee_leave`.`leaveid` = `db_leave_overtime`.`leaveid` INNER JOIN `db_employee_overtime` ON `db_employee_overtime`.`overtimeid` = `db_leave_overtime`.`overtimeid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_leave_overtime`.`employeeid` INNER JOIN `db_employee` AS `db_applyer` ON `db_applyer`.`employeeid` = `db_employee_leave`.`applyer` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_leave_overtime`.`lvid` DESC" . $pages->limitsql;
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
<title>人事系统-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>请假加班抵扣记录</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>申请人：</th>
        <td><input type="text" name="applyer_name" class="input_txt" /></td>
        <th>加班单号：</th>
        <td><input type="text" name="overtime_num" class="input_txt" /></td>
        <th>请假单号：</th>
        <td><input type="text" name="leave_num" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="employee_leave_overtimedo.php" name="employee_leave_overtime_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">申请人</th>
        <th width="10%">请假单号</th>
        <th width="10%">请假日期</th>
        <th width="8%">请假小时(H)</th>
        <th width="10%">加班单号</th>
        <th width="10%">加班日期</th>
        <th width="8%">加班小时(H)</th>
        <th width="8%">抵扣小时(H)</th>
        <th width="10%">操作人</th>
        <th width="12%">操作时间</th>
      </tr>
      <?php while($row = $result->fetch_assoc()){ ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $row['lvid']; ?>" /></td>
        <td><?php echo $row['applyer_name']; ?></td>
        <td><?php echo $row['leave_num']; ?></td>
        <td><?php echo $row['leave_applydate']; ?></td>
        <td><?php echo $row['leavetime']; ?></td>
        <td><?php echo $row['overtime_num']; ?></td>
        <td><?php echo $row['overtime_applydate']; ?></td>
        <td><?php echo $row['overtime']; ?></td>
        <td><?php echo $row['deduction_time']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['dotime']; ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div>
  </form>
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