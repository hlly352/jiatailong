<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$mouldid = fun_check_int($_GET['id']);
$sql_mould = "SELECT `db_mould`.`mouldid`,`db_mould`.`project_name`,`db_mould`.`mould_number`,`db_client`.`client_code`,`db_mould_status`.`mould_statusname` FROM `db_mould` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` INNER JOIN `db_mould_status` ON `db_mould_status`.`mould_statusid` = `db_mould`.`mould_statusid` WHERE `db_mould`.`mouldid` = '$mouldid'";
$result_mould = $db->query($sql_mould);
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
<title>模具数据-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result_mould->num_rows){
	  $array_mould = $result_mould->fetch_assoc();
  ?>
  <h4>模具信息</h4>
  <table>
    <tr>
      <th width="12%">代码：</th>
      <td width="12%"><?php echo $array_mould['client_code']; ?></td>
      <th width="12%">项目名称：</th>
      <td width="12%"><?php echo $array_mould['project_name']; ?></td>
      <th width="12%">模具编号：</th>
      <td width="12%"><?php echo $array_mould['mould_number']; ?></td>
      <th width="12%">目前状态：</th>
      <td width="12%"><?php echo $array_mould['mould_statusname']; ?></td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php
$sql_mould_material = "SELECT `material_date`,`material_list_number`,`material_list_sn`,`material_number`,`material_name`,`specification`,`material_quantity`,`texture`,`hardness`,`brand`,`spare_quantity`,`remark` FROM `db_mould_material` WHERE `mouldid` = '$mouldid' ORDER BY `materialid` ASC";
$result_mould_material = $db->query($sql_mould_material);
?>
<div id="table_list">
  <?php if($result_mould_material->num_rows){ ?>
  <table>
    <caption>
    物料清单
    </caption>
    <tr>
      <th width="4%">序号</th>
      <th width="6%">下单日期</th>
      <th width="8%">料单编号</th>
      <th width="4%">料单序号</th>
      <th width="10%">物料编码</th>
      <th width="10%">物料名称</th>
      <th width="12%">规格</th>
      <th width="4%">数量</th>
      <th width="8%">材质</th>
      <th width="8%">硬度</th>
      <th width="8%">品牌</th>
      <th width="4%">备件数量</th>
      <th width="14%">备注</th>
    </tr>
    <?php
	$i = 1;
	while($row_mould_material = $result_mould_material->fetch_assoc()){
	?>
    <tr>
      <td><?php echo $i; ?></td>
      <td><?php echo $row_mould_material['material_date']; ?></td>
      <td><?php echo $row_mould_material['material_list_number']; ?></td>
      <td><?php echo $row_mould_material['material_list_sn']; ?></td>
      <td><?php echo $row_mould_material['material_number']; ?></td>
      <td><?php echo $row_mould_material['material_name']; ?></td>
      <td><?php echo $row_mould_material['specification']; ?></td>
      <td><?php echo $row_mould_material['material_quantity']; ?></td>
      <td><?php echo $row_mould_material['texture']; ?></td>
      <td><?php echo $row_mould_material['hardness']; ?></td>
      <td><?php echo $row_mould_material['brand']; ?></td>
      <td><?php echo $row_mould_material['spare_quantity']; ?></td>
      <td><?php echo $row_mould_material['remark']; ?></td>
    </tr>
    <?php
	$i++;
	}
	?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无模具物料记录！</p>";
  }
  ?>
</div>
<?php
$sql_outward = "SELECT `db_mould_outward`.`outwardid`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`cost`,`db_mould_outward`.`applyer`,`db_mould_outward`.`plan_date`,`db_mould_outward`.`actual_date`,`db_mould_outward`.`inout_status`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_mould_outward_type`.`outward_typename` FROM `db_mould_outward` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_outward`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_outward`.`workteamid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_mould_outward`.`outward_typeid` WHERE `db_mould_outward`.`mouldid` = '$mouldid' AND `db_mould_outward`.`outward_status` = 1 ORDER BY `db_mould_outward`.`order_date` DESC,`db_mould_outward`.`outwardid` ASC";
$result_outward = $db->query($sql_outward);
?>
<div id="table_list">
  <?php if($result_outward->num_rows){ ?>
  <table>
    <caption>
    外协加工记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">模具编号</th>
      <th width="12%">零件编号</th>
      <th width="6%">外协时间</th>
      <th width="6%">申请组别</th>
      <th width="10%">外协单号</th>
      <th width="6%">数量</th>
      <th width="10%">供应商</th>
      <th width="6%">类型</th>
      <th width="6%">金额</th>
      <th width="8%">申请人</th>
      <th width="6%">计划回厂</th>
      <th width="6%">实际回厂</th>
      <th width="4%">进度状态</th>
    </tr>
    <?php
	while($row_outward = $result_outward->fetch_assoc()){
		$inout_status = $row_outward['inout_status'];
		$actual_date = $inout_status?$row_outward['actual_date']:'--';
	?>
    <tr>
      <td><?php echo $row_outward['outwardid']; ?></td>
      <td><?php echo $row_outward['mould_number']; ?></td>
      <td><?php echo $row_outward['part_number']; ?></td>
      <td><?php echo $row_outward['order_date']; ?></td>
      <td><?php echo $row_outward['workteam_name']; ?></td>
      <td><?php echo $row_outward['order_number']; ?></td>
      <td><?php echo $row_outward['quantity']; ?></td>
      <td><?php echo $row_outward['supplier_cname']; ?></td>
      <td><?php echo $row_outward['outward_typename']; ?></td>
      <td><?php echo $row_outward['cost']; ?></td>
      <td><?php echo $row_outward['applyer']; ?></td>
      <td><?php echo $row_outward['plan_date']; ?></td>
      <td><?php echo $actual_date; ?></td>
      <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无外协加工记录！</p>";
  }
  ?>
</div>
<?php
$sql_weld = "SELECT `db_mould_weld`.`weldid`,`db_mould_weld`.`part_number`,`db_mould_weld`.`order_date`,`db_mould_weld`.`order_number`,`db_mould_weld`.`quantity`,`db_mould_weld`.`weld_cause`,`db_mould_weld`.`cost`,`db_mould_weld`.`applyer`,`db_mould_weld`.`plan_date`,`db_mould_weld`.`actual_date`,`db_mould_weld`.`inout_status`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_responsibility_team`.`team_name`,`db_mould_weld_type`.`weld_typename` FROM `db_mould_weld` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_weld`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_weld`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_weld`.`workteamid` INNER JOIN `db_responsibility_team` ON `db_responsibility_team`.`teamid` = `db_mould_weld`.`teamid` INNER JOIN `db_mould_weld_type` ON `db_mould_weld_type`.`weld_typeid` = `db_mould_weld`.`weld_typeid` WHERE `db_mould_weld`.`mouldid` = '$mouldid' AND `db_mould_weld`.`weld_status` = 1 ORDER BY `db_mould_weld`.`order_date` DESC,`db_mould_weld`.`weldid` ASC";
$result_weld = $db->query($sql_weld);
?>
<div id="table_list">
  <?php if($result_weld->num_rows){ ?>
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
      <th width="8%">外协单号</th>
      <th width="4%">数量</th>
      <th width="10%">烧焊原因</th>
      <th width="6%">责任组别</th>
      <th width="8%">供应商</th>
      <th width="4%">类型</th>
      <th width="4%">金额</th>
      <th width="6%">申请人</th>
      <th width="6%">计划回厂</th>
      <th width="6%">实际回厂</th>
      <th width="4%">进度状态</th>
    </tr>
    <?php
    while($row_weld = $result_weld->fetch_assoc()){
		$inout_status = $row_weld['inout_status'];
		$actual_date = $inout_status?$row_weld['actual_date']:'--';
	?>
    <tr>
    <tr>
      <td><?php echo $row_weld['weldid']; ?></td>
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
      <td><?php echo $actual_date; ?></td>
      <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无零件烧焊记录！</p>";
  }
  ?>
</div>
<?php
$sql_try = "SELECT `db_mould_try`.`tryid`,`db_mould_try`.`order_number`,`db_mould_try`.`try_date`,CONCAT('T',`db_mould_try`.`try_times`) AS `try_times`,CONCAT('T',`db_mould_try`.`tonnage`) AS `tonnage`,`db_mould_try`.`unit_price`,`db_mould_try`.`cost`,`db_mould_try`.`remark`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_try_cause`.`try_causename` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_try`.`supplierid` INNER JOIN `db_mould_try_cause` ON `db_mould_try_cause`.`try_causeid` = `db_mould_try`.`try_causeid` WHERE `db_mould_try`.`mouldid` = '$mouldid' AND `db_mould_try`.`try_status` = 1 ORDER BY `db_mould_try`.`try_date` DESC,`db_mould_try`.`tryid` ASC";
$result_try = $db->query($sql_try);
?>
<div id="table_list">
  <?php if($result_try->num_rows){ ?>
  <table>
    <caption>
    模具试模记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">模具编号</th>
      <th width="12%">供应商</th>
      <th width="10%">送货单号</th>
      <th width="8%">试模日期</th>
      <th width="6%">试模次数</th>
      <th width="16%">试模原因</th>
      <th width="6%">啤机吨位</th>
      <th width="6%">含税单价</th>
      <th width="6%">金额</th>
      <th width="16%">备注</th>
    </tr>
    <?php while($row_try = $result_try->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_try['tryid']; ?></td>
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
    </tr>
    <?php } ?>
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