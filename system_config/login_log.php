<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
if($_GET['submit']){
	$employee_name = trim($_GET['employee_name']);
	$account = trim($_GET['account']);
	$sqlwhere = " WHERE `db_employee`.`employee_name` LIKE '%$employee_name%' AND `db_login_log`.`account` LIKE '%$account%'";
}
$sql = "SELECT `db_login_log`.`logid`,`db_login_log`.`employeeid`,`db_login_log`.`account`,`db_login_log`.`login_status`,`db_login_log`.`ip`,`db_login_log`.`dotime`,`db_employee`.`employee_name` FROM `db_login_log` LEFT JOIN `db_employee` ON `db_employee`.`employeeid` = `db_login_log`.`employeeid` $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,12);
$sqllist = $sql . " ORDER BY `db_login_log`.`logid` DESC" . $pages->limitsql;
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
<title>系统设置-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>登录日志</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>员工：</th>
        <td><input type="text" name="employee_name" class="input_txt" /></td>
        <th>账号：</th>
        <td><input type="text" name="account" class="input_txt" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="日报表" class="button" onclick="location.href='report_login_day.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="20%">员工</th>
      <th width="20%">账号</th>
      <th width="16%">状态</th>
      <th width="20%">IP</th>
      <th width="20%">时间</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		$employee_name = $row['employeeid']?$row['employee_name']:'--';
		$login_status = $array_login_status[$row['login_status']];
		$login_status = ($row['login_status'] != 'A')?"<font color=\"red\">".$login_status."</font>":$login_status;
	?>
    <tr>
      <td><?php echo $row['logid']; ?></td>
      <td><?php echo $employee_name; ?></td>
      <td><?php echo $row['account']; ?></td>
      <td><?php echo $login_status; ?></td>
      <td><?php echo $row['ip']; ?></td>
      <td><?php echo $row['dotime']; ?></td>
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