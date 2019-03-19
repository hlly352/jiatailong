<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$mouldid = fun_check_int($_GET['id']);
$sql = "SELECT `db_mould`.`mouldid`,`db_mould`.`project_name`,`db_mould`.`mould_number`,`db_client`.`client_code`,`db_mould_status`.`mould_statusname` FROM `db_mould` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` INNER JOIN `db_mould_status` ON `db_mould_status`.`mould_statusid` = `db_mould`.`mould_statusid` WHERE `db_mould`.`mouldid` = '$mouldid'";
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
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<title>模具加工-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
  ?>
  <h4>模具信息</h4>
  <table>
    <tr>
      <th width="10%">代码：</th>
      <td width="15%"><?php echo $array['client_code']; ?></td>
      <th width="10%">项目名称：</th>
      <td width="15%"><?php echo $array['project_name']; ?></td>
      <th width="10%">模具编号：</th>
      <td width="15%"><?php echo $array['mould_number']; ?></td>
      <th width="10%">目前状态：</th>
      <td width="15%"><?php echo $array['mould_statusname']; ?></td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php
$sql_outward = "SELECT `db_mould_outward`.`outwardid`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`cost`,`db_mould_outward`.`applyer`,`db_mould_outward`.`plan_date`,`db_mould_outward`.`actual_date`,`db_mould_outward`.`inout_status`,`db_mould_outward`.`supplierid`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_mould_outward_type`.`outward_typename` FROM `db_mould_outward` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` LEFT JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_outward`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_outward`.`workteamid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_mould_outward`.`outward_typeid` WHERE `db_mould_outward`.`mouldid` = '$mouldid' AND `db_mould_outward`.`outward_status` = 1 ORDER BY `db_mould_outward`.`order_date` ASC,`db_mould_outward`.`outwardid` ASC";
$result_outward = $db->query($sql_outward);
?>
<div id="table_list">
  <?php
  if($result_outward->num_rows){
	  //计算总费用
	  $sql_outward_cost = "SELECT SUM(`cost`) AS `total_cost` FROM `db_mould_outward` WHERE `db_mould_outward`.`mouldid` = '$mouldid' AND `db_mould_outward`.`outward_status` = 1";
	  $result_outward_cost = $db->query($sql_outward_cost);
	  $array_outward_cost = $result_outward_cost->fetch_assoc()
  ?>
  <table>
    <caption>
    外协加工记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="7%">模具编号</th>
      <th width="14%">零件编号</th>
      <th width="7%">外协时间</th>
      <th width="8%">申请组别</th>
      <th width="7%">外协单号</th>
      <th width="4%">数量</th>
      <th width="9%">供应商</th>
      <th width="6%">类型</th>
      <th width="6%">总金额</th>
      <th width="6%">申请人</th>
      <th width="7%">计划回厂</th>
      <th width="7%">实际回厂</th>
      <th width="4%">进度状态</th>
      <th width="4%">Info</th>
    </tr>
    <?php
	while($row_outward = $result_outward->fetch_assoc()){
		$outwardid = $row_outward['outwardid'];
		$inout_status = $row_outward['inout_status'];
		$actual_date = $inout_status?$row_outward['actual_date']:'--';
    $supplier_cname = $row_outward['supplierid'] ? $row_outward['supplier_cname'] : '--';
	?>
    <tr>
      <td><?php echo $outwardid; ?></td>
      <td><?php echo $row_outward['mould_number']; ?></td>
      <td><?php echo $row_outward['part_number']; ?></td>
      <td><?php echo $row_outward['order_date']; ?></td>
      <td><?php echo $row_outward['workteam_name']; ?></td>
      <td><?php echo $row_outward['order_number']; ?></td>
      <td><?php echo $row_outward['quantity']; ?></td>
      <td><?php echo $supplier_cname; ?></td>
      <td><?php echo $row_outward['outward_typename']; ?></td>
      <td><?php echo $row_outward['cost']; ?></td>
      <td><?php echo $row_outward['applyer']; ?></td>
      <td><?php echo $row_outward['plan_date']; ?></td>
      <td><?php echo $actual_date; ?></td>
      <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
      <td><a href="mould_outward_info.php?id=<?php echo $outwardid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="9">Total</td>
      <td><?php echo $array_outward_cost['total_cost']; ?></td>
      <td colspan="5">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无外协加工记录！</p>";
  }
  ?>
</div>
<?php
$sql_weld = "SELECT `db_mould_weld`.`weldid`,`db_mould_weld`.`part_number`,`db_mould_weld`.`order_date`,`db_mould_weld`.`order_number`,`db_mould_weld`.`quantity`,`db_mould_weld`.`weld_cause`,`db_mould_weld`.`cost`,`db_mould_weld`.`applyer`,`db_mould_weld`.`plan_date`,`db_mould_weld`.`inout_status`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_responsibility_team`.`team_name`,`db_mould_weld_type`.`weld_typename` FROM `db_mould_weld` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_weld`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_weld`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_weld`.`workteamid` INNER JOIN `db_responsibility_team` ON `db_responsibility_team`.`teamid` = `db_mould_weld`.`teamid` INNER JOIN `db_mould_weld_type` ON `db_mould_weld_type`.`weld_typeid` = `db_mould_weld`.`weld_typeid` WHERE `db_mould_weld`.`mouldid` = '$mouldid' AND `db_mould_weld`.`weld_status` = 1 ORDER BY `db_mould_weld`.`order_date` ASC,`db_mould_weld`.`weldid` ASC";
$result_weld = $db->query($sql_weld);
?>
<div id="table_list">
  <?php
  if($result_weld->num_rows){
	  //计算总费用
	  $sql_weld_cost = "SELECT SUM(`cost`) AS `total_cost` FROM `db_mould_weld` WHERE `db_mould_weld`.`mouldid` = '$mouldid' AND `db_mould_weld`.`weld_status` = 1";
	  $result_weld_cost = $db->query($sql_weld_cost);
	  $array_weld_cost = $result_weld_cost->fetch_assoc();
  ?>
  <table>
    <caption>
    零件烧焊记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">模具编号</th>
      <th width="10%">零件编号</th>
      <th width="6%">外发时间</th>
      <th width="6%">申请组别</th>
      <th width="6%">外协单号</th>
      <th width="4%">数量</th>
      <th width="10%">烧焊原因</th>
      <th width="8%">责任组别</th>
      <th width="8%">供应商</th>
      <th width="6%">加工类型</th>
      <th width="4%">金额</th>
      <th width="6%">申请人</th>
      <th width="6%">计划回厂</th>
      <th width="4%">回厂状态</th>
      <th width="4%">Info</th>
    </tr>
    <?php
	while($row_weld = $result_weld->fetch_assoc()){
		$weldid = $row_weld['weldid'];
	?>
    <tr>
      <td><?php echo $weldid; ?></td>
      <td><?php echo $row_weld['mould_number']; ?></td>
      <td><?php echo $row_weld['part_number']; ?></td>
      <td><?php echo $row_weld['order_date']; ?></td>
      <td><?php echo $row_weld['workteam_name']; ?></td>
      <td><?php echo $row_weld['order_number']; ?></td>
      <td><?php echo $row_weld['quantity']; ?></td>
      <td><?php echo $row_weld['weld_cause']; ?></td>
      <td><?php echo $row_weld['team_name']; ?></td>
      <td><?php echo $row_weld['supplier_cname']; ?></td>
      <td><?php echo $row_weld['weld_typename']; ?></td>
      <td><?php echo $row_weld['cost']; ?></td>
      <td><?php echo $row_weld['applyer']; ?></td>
      <td><?php echo $row_weld['plan_date']; ?></td>
      <td><?php echo $array_mould_inout_status[$row_weld['inout_status']]; ?></td>
      <td><a href="mould_weld_info.php?id=<?php echo $weldid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="11">Total</td>
      <td><?php echo $array_weld_cost['total_cost']; ?></td>
      <td colspan="4">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无零件烧焊记录！</p>";
  }
  ?>
</div>
<?php
$sql_try = "SELECT `db_mould_try`.`tryid`,`db_mould_try`.`order_number`,`db_mould_try`.`try_date`,CONCAT('T',`db_mould_try`.`try_times`) AS `try_times`,CONCAT('T',`db_mould_try`.`tonnage`) AS `tonnage`,`db_mould_try`.`unit_price`,`db_mould_try`.`cost`,`db_mould_try`.`remark`,`db_mould_try`.`try_status`,`db_mould_try_cause`.`try_causename`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_try`.`supplierid` INNER JOIN `db_mould_try_cause` ON `db_mould_try_cause`.`try_causeid` = `db_mould_try`.`try_causeid` WHERE `db_mould_try`.`mouldid` = '$mouldid' AND `db_mould_try`.`try_status` = 1 ORDER BY `db_mould_try`.`try_date` ASC,`db_mould_try`.`tryid` ASC";
$result_try = $db->query($sql_try);
?>
<div id="table_list">
  <?php
  if($result_try->num_rows){
	  //计算总费用
	  $sql_try_cost = "SELECT SUM(`cost`) AS `total_cost` FROM `db_mould_try` WHERE `db_mould_try`.`mouldid` = '$mouldid' AND `db_mould_try`.`try_status` = 1";
	  $result_try_cost = $db->query($sql_try_cost);
	  $array_try_cost = $result_try_cost->fetch_assoc();
  ?>
  <table>
    <caption>
    模具试模记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">模具编号</th>
      <th width="8%">供应商</th>
      <th width="8%">送货单号</th>
      <th width="10%">试模日期</th>
      <th width="8%">试模次数</th>
      <th width="12%">试模原因</th>
      <th width="6%">啤机吨位</th>
      <th width="6%">含税单价</th>
      <th width="6%">金额</th>
      <th width="18%">备注</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row_try = $result_try->fetch_assoc()){
		$tryid = $row_try['tryid'];
	?>
    <tr>
      <td><?php echo $tryid; ?></td>
      <td><?php echo $row_try['mould_number']; ?></td>
      <td><?php echo $row_try['supplier_cname']; ?></td>
      <td><?php echo $row_try['order_number']; ?></td>
      <td><?php echo $row_try['try_date']; ?></td>
      <td><?php echo $row_try['try_times']; ?></td>
      <td><?php echo $row_try['try_causename']; ?></td>
      <td><?php echo $row_try['tonnage']; ?></td>
      <td><?php echo $row_try['unit_price']; ?></td>
      <td><?php echo $row_try['cost']; ?></td>
      <td><?php echo $row_try['remark']; ?></td>
      <td><a href="mould_try_info.php?id=<?php echo $tryid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="9">Total</td>
      <td><?php echo $array_try_cost['total_cost']; ?></td>
      <td colspan="2">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无模具试模记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>