<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$listid = fun_check_int($_GET['id']);
$sql = "SELECT `db_material_order_list`.`listid`,`db_material_order_list`.`order_quantity`,`db_material_order_list`.`actual_quantity`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`tax_rate`,`db_material_order_list`.`process_cost`,`db_material_order_list`.`iscash`,`db_material_order_list`.`plan_date`,`db_material_order_list`.`remark`,ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2) AS `amount`,(`db_material_order_list`.`process_cost`+ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2)) AS `total_amount`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name`,`db_unit_actual`.`unit_name` AS `actual_unit_name` FROM `db_material_order_list` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_order_list`.`listid` = '$listid'";
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
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$listid = $array['listid'];
?>
<div id="table_sheet">
  <h4>物料订单信息</h4>
  <table>
    <tr>
      <th>合同号：</th>
      <td colspan="7"><?php echo $array['order_number']; ?></td>
    </tr>
    <tr>
      <th width="10%">模具编号：</th>
      <td width="15%"><?php echo $array['mould_number']; ?></td>
      <th width="10%">物料名称：</th>
      <td width="15%"><?php echo $array['material_name']; ?></td>
      <th width="10%">规格：</th>
      <td width="15%"><?php echo $array['specification']; ?></td>
      <th width="10%">材质：</th>
      <td width="15%"><?php echo $array['texture']; ?></td>
    </tr>
    <tr>
      <th>需求数量：</th>
      <td><?php echo $array['order_quantity'].$array['unit_name']; ?></td>
      <th>实际数量：</th>
      <td><?php echo $array['actual_quantity'].$array['actual_unit_name']; ?></td>
      <th>单价(含税)：</th>
      <td><?php echo $array['unit_price']; ?></td>
      <th>税率：</th>
      <td><?php echo ($array['tax_rate']*100).'%'; ?></td>
    </tr>
    <tr>
      <th>金额(含税)：</th>
      <td><?php echo $array['amount']; ?></td>
      <th>加工费：</th>
      <td><?php echo $array['process_cost']; ?></td>
      <th>总计：</th>
      <td><?php echo $array['total_amount']; ?></td>
      <th>现金：</th>
      <td><?php echo $array_is_status[$array['iscash']]; ?></td>
    </tr>
    <tr>
      <th>供应商：</th>
      <td><?php echo $array['supplier_cname']; ?></td>
      <th>订单日期：</th>
      <td><?php echo $array['order_date']; ?></td>
      <th>计划回厂时间：</th>
      <td><?php echo $array['plan_date']; ?></td>
      <th>备注：</th>
      <td><?php echo $array['remark']; ?></td>
    </tr>
  </table>
</div>
<?php
$sql_pay = "SELECT `db_cash_pay`.`payid`,`db_cash_pay`.`pay_date`,`db_cash_pay`.`pay_amount`,`db_cash_pay`.`dotime`,`db_cash_pay`.`remark`,`db_employee`.`employee_name` FROM `db_cash_pay` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cash_pay`.`employeeid` WHERE `db_cash_pay`.`linkid` = '$listid' AND `db_cash_pay`.`data_type` = 'M' ORDER BY `db_cash_pay`.`pay_date` DESC,`db_cash_pay`.`payid` DESC";
$result_pay = $db->query($sql_pay);
?>
<div id="table_list">
  <?php if($result_pay->num_rows){ ?>
  <table>
    <caption>
    付款记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="16%">付款日期</th>
      <th width="16%">付款金额</th>
      <th width="16%">付款人</th>
      <th width="20%">操作时间</th>
      <th width="28%">备注</th>
    </tr>
    <?php
    while($row_pay = $result_pay->fetch_assoc()){
		$pay_amount = $row_pay['pay_amount'];
	?>
    <tr<?php echo ($row_pay['payid'] == $payid)?" style=\"background:#DCD9FD\"":''; ?>>
      <td><?php echo $row_pay['payid']; ?></td>
      <td><?php echo $row_pay['pay_date']; ?></td>
      <td><?php echo $pay_amount; ?></td>
      <td><?php echo $row_pay['employee_name']; ?></td>
      <td><?php echo $row_pay['dotime']; ?></td>
      <td><?php echo $row_pay['remark']; ?></td>
    </tr>
    <?php
	$all_pay_amount += $pay_amount;
    }
	?>
    <tr>
      <td colspan="2">Total</td>
      <td><?php echo number_format($all_pay_amount,2); ?></td>
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无付款记录！</p>";
  }
  ?>
</div>
<?php
$sql_inout = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`form_number`,`db_material_inout`.`dodate`,`db_material_inout`.`dotype`,`db_material_inout`.`quantity`,IF(`db_material_inout`.`dotype` = 'I',`db_material_inout`.`inout_quantity`,'-') AS `inout_quantity`,`db_material_inout`.`remark`,`db_material_inout`.`dotime`,IF(`db_material_inout`.`dotype` = 'I',`db_material_order_list`.`unit_price`,'-') AS `unit_price`,IF(`db_material_inout`.`dotype` = 'I',`db_material_inout`.`amount`,'-') AS `amount`,`db_unit_order`.`unit_name` AS `unit_name_order`,IF(`db_material_inout`.`dotype` = 'I',`db_unit_actual`.`unit_name`,'') AS `unit_name_actual`,`db_employee`.`employee_name` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_inout`.`employeeid` WHERE `db_material_inout`.`listid` = '$listid' ORDER BY `db_material_inout`.`inoutid` DESC";
$result_inout = $db->query($sql_inout);
?>
<div id="table_list">
  <?php if($result_inout->num_rows){ ?>
  <table>
    <caption>
    出入库记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="10%">表单号</th>
      <th width="8%">类型</th>
      <th width="8%">订单数量</th>
      <th width="6%">实际数量</th>
      <th width="8%">单价(含税)</th>
      <th width="8%">金额(含税)</th>
      <th width="10%">出入库日期</th>
      <th width="10%">操作人</th>
      <th width="14%">操作时间</th>
      <th width="14%">备注</th>
    </tr>
    <?php
	$surplus = 0;
	while($row_inout = $result_inout->fetch_assoc()){
		$inoutid = $row_inout['inoutid'];
		$dotype = $row_inout['dotype'];
		$quantity = ($dotype == 'I')?$row_inout['quantity']:(-$row_inout['quantity']);
	?>
    <tr>
      <td><?php echo $inoutid; ?></td>
      <td><?php echo $row_inout['form_number']; ?></td>
      <td><?php echo $array_inout_dotype[$dotype]; ?></td>
      <td><?php echo $row_inout['quantity'].$row_inout['unit_name_order']; ?></td>
      <td><?php echo $row_inout['inout_quantity'].$row_inout['unit_name_actual']; ?></td>
      <td><?php echo $row_inout['unit_price']; ?></td>
      <td><?php echo $row_inout['amount']; ?></td>
      <td><?php echo $row_inout['dodate']; ?></td>
      <td><?php echo $row_inout['employee_name']; ?></td>
      <td><?php echo $row_inout['dotime']; ?></td>
      <td><?php echo $row_inout['remark']; ?></td>
    </tr>
    <?php
	$surplus += $quantity;
	}
	?>
    <tr>
      <td colspan="3">结余</td>
      <td><?php echo number_format($surplus,2).$array['unit_name']; ?></td>
      <td colspan="7">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无出入库记录</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>