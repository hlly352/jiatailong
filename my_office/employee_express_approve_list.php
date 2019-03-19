<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_GET['submit']){
	$express_num = trim($_GET['express_num']);
	$applyer_name = trim($_GET['applyer_name']);
	$sqlwhere = " AND `db_employee_express`.`express_num` LIKE '%$express_num%' AND `db_employee`.`employee_name` LIKE '%$applyer_name%'";
}
$sql = "SELECT `db_employee_express`.`expressid`,`db_employee_express`.`express_num`,`db_employee_express`.`applyer`,`db_employee_express`.`apply_date`,`db_employee_express`.`consignee_inc`,`db_employee_express`.`express_item`,`db_employee_express`.`paytype`,`db_employee`.`employee_name`,`db_express_inc`.`inc_cname` FROM `db_employee_express` INNER JOIN `db_express_inc` ON `db_express_inc`.`incid` = `db_employee_express`.`express_incid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_employee_express`.`applyer` WHERE `db_employee_express`.`approve_status` = 'A' AND `db_employee_express`.`express_status` = 1 AND (`db_employee`.`superior` = '$employeeid' OR (`db_employee`.`position_type` IN ('A','B') AND `db_employee_express`.`applyer` = '$employeeid')) $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_employee_express`.`apply_date` DESC,`db_employee_express`.`expressid` DESC" . $pages->limitsql;
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
  <h4>我的待审批快递</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>快递单号：</th>
        <td><input type="text" name="express_num" class="input_txt" /></td>
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
      <th width="10%">快递单号</th>
      <th width="10%">申请人</th>
      <th width="10%">申请日期</th>
      <th width="10%">快递公司</th>
      <th width="16%">收件人公司</th>
      <th width="28%">快递物品</th>
      <th width="8%">付款方式</th>
      <th width="4%">审批</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$expressid = $row['expressid'];
	?>
    <tr>
      <td><?php echo $expressid; ?></td>
      <td><?php echo $row['express_num']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['apply_date']; ?></td>
      <td><?php echo $row['inc_cname']; ?></td>
      <td><?php echo $row['consignee_inc']; ?></td>
      <td><?php echo $row['express_item']; ?></td>
      <td><?php echo $array_express_paytype[$row['paytype']];; ?></td>
      <td><a href="employee_express_approve.php?id=<?php echo $expressid; ?>"><img src="../images/system_ico/approve_10_10.png" width="10" height="10" /></a></td>
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