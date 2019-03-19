<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$listid = fun_check_int($_GET['id']);
$sql = "SELECT `db_cutter_order_list`.`unit_price`,`db_cutter_order_list`.`tax_rate`,`db_cutter_order_list`.`iscash`,`db_cutter_order_list`.`plan_date`,`db_cutter_order_list`.`remark`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_cutter_purchase_list`.`quantity`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_cutter_purchase`.`purchase_number`,`db_cutter_purchase`.`purchase_date`,`db_supplier`.`supplier_cname`,`db_order`.`employee_name` AS `order_employee_name`,`db_purchase`.`employee_name` AS `purchase_employee_name`,(`db_cutter_purchase_list`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_cutter_order_list` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_employee` AS `db_order` ON `db_order`.`employeeid` = `db_cutter_order`.`employeeid` INNER JOIN `db_employee` AS `db_purchase` ON `db_purchase`.`employeeid` = `db_cutter_purchase`.`employeeid` WHERE `db_cutter_order_list`.`listid` = '$listid'";
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
<title>财务管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
?>
<div id="table_sheet">
  <h4>刀具订单信息</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="15%"><?php echo $array['order_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo $array['order_date']; ?></td>
      <th width="10%">供应商：</th>
      <td width="15%"><?php echo $array['supplier_cname']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array['order_employee_name']; ?></td>
    </tr>
    <tr>
      <th>申购单号：</th>
      <td><?php echo $array['purchase_number']; ?></td>
      <th>申购日期：</th>
      <td><?php echo $array['purchase_date']; ?></td>
      <th>申购人：</th>
      <td colspan="3"><?php echo $array['purchase_employee_name']; ?></td>
    </tr>
    <tr>
      <th>类型：</th>
      <td><?php echo $array['type']; ?></td>
      <th>规格：</th>
      <td><?php echo $array['specification']; ?></td>
      <th>材质：</th>
      <td><?php echo $array_cutter_texture[$array['texture']]; ?></td>
      <th>硬度：</th>
      <td><?php echo $array['specification']; ?></td>
    </tr>
    <tr>
      <th>品牌：</th>
      <td><?php echo $array['brand']; ?></td>
      <th>数量：</th>
      <td><?php echo $array['quantity']; ?></td>
      <th>单价：</th>
      <td><?php echo $array['unit_price']; ?></td>
      <th>金额：</th>
      <td><?php echo $array['amount']; ?></td>
    </tr>
    <tr>
      <th>税率：</th>
      <td><?php echo $array['tax_rate']; ?></td>
      <th>现金：</th>
      <td><?php echo $array['iscash']; ?></td>
      <th>计划回厂日期：</th>
      <td><?php echo $array['plan_date']; ?></td>
      <th>备注：</th>
      <td><?php echo $array['remark']; ?></td>
    </tr>
  </table>
</div>
<?php
$sql_pay = "SELECT `db_cash_pay`.`payid`,`db_cash_pay`.`pay_date`,`db_cash_pay`.`pay_amount`,`db_cash_pay`.`dotime`,`db_cash_pay`.`remark`,`db_employee`.`employee_name` FROM `db_cash_pay` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cash_pay`.`employeeid` WHERE `db_cash_pay`.`linkid` = '$listid' AND `db_cash_pay`.`data_type` = 'MC' ORDER BY `db_cash_pay`.`pay_date` DESC,`db_cash_pay`.`payid` DESC";
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
$sql_inout = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`form_number`,`db_cutter_inout`.`dotype`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`dotime`,`db_cutter_inout`.`remark`,`db_employee`.`employee_name` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_inout`.`employeeid` WHERE `db_cutter_inout`.`listid` = '$listid' ORDER BY `db_cutter_inout`.`inoutid` DESC";
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
      <th width="12%">表单号</th>
      <th width="10%">类型</th>
      <th width="10%">出入数量</th>
      <th width="6%">单位</th>
      <th width="12%">出入库日期</th>
      <th width="12%">操作人</th>
      <th width="14%">操作时间</th>
      <th width="20%">备注</th>
    </tr>
    <?php
	$surplus = 0;
    while($row_inout = $result_inout->fetch_assoc()){
		$dotype = $row_inout['dotype'];
		$quantity = ($dotype == 'I')?$row_inout['quantity']:(-$row_inout['quantity']);
	?>
    <tr>
      <td><?php echo $row_inout['inoutid']; ?></td>
      <td><?php echo $row_inout['form_number']; ?></td>
      <td><?php echo $array_inout_dotype[$dotype]; ?></td>
      <td><?php echo $row_inout['quantity']; ?></td>
      <td>件</td>
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
      <td><?php echo $surplus; ?></td>
      <td colspan="5">&nbsp;</td>
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