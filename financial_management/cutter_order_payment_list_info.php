<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
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
$payid = fun_check_int($_GET['id']);
$sql = "SELECT `db_cutter_purchase_list`.`quantity`,`db_cutter_order_list`.`listid`,`db_cutter_order_list`.`unit_price`,`db_cutter_order_list`.`tax_rate`,`db_cutter_order_list`.`plan_date`,`db_cutter_order_list`.`remark` AS `order_remark`,ROUND(`db_cutter_purchase_list`.`quantity`*`db_cutter_order_list`.`unit_price`,2) AS `amount`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,`db_cash_pay`.`pay_date`,`db_cash_pay`.`pay_amount`,`db_cash_pay`.`remark` AS `pay_remark` FROM `db_cash_pay` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cash_pay`.`linkid` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` WHERE `db_cash_pay`.`payid` = '$payid' AND `db_cash_pay`.`data_type` = 'MC'";
$result = $db->query($sql);
if($result->num_rows){
	$array = $result->fetch_assoc();
	$listid = $array['listid'];
	$amount = $array['amount'];
	$order_date = $array['order_date'];
	$sql_pay_amount = "SELECT SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` = '$listid' AND `data_type` = 'MC' GROUP BY `linkid`";
	$result_pay_amount = $db->query($sql_pay_amount);
	if($result_pay_amount->num_rows){
		$array_pay_amount = $result_pay_amount->fetch_assoc();
		$total_pay_amount = $array_pay_amount['total_pay_amount'];
	}else{
		$total_pay_amount = 0;
	}
	$wait_pay_amount = number_format(($amount - $total_pay_amount),2);
?>
<div id="table_sheet">
  <h4>刀具付款订单信息</h4>
  <table>
    <tr>
      <th>合同号：</th>
      <td colspan="7"><?php echo $array['order_number']; ?></td>
    </tr>
    <tr>
      <th width="10%">类型：</th>
      <td width="15%"><?php echo $array['type']; ?></td>
      <th width="10%">规格：</th>
      <td width="15%"><?php echo $array['specification']; ?></td>
      <th width="10%">材质：</th>
      <td width="15%"><?php echo $array_cutter_texture[$array['texture']]; ?></td>
      <th width="10%">硬度：</th>
      <td width="15%"><?php echo $array['hardness']; ?></td>
    </tr>
    <tr>
      <th>品牌：</th>
      <td><?php echo $array['brand']; ?></td>
      <th>数量：</th>
      <td><?php echo $array['quantity']; ?> 件</td>
      <th>单价(含税)：</th>
      <td><?php echo $array['unit_price']; ?></td>
      <th>金额(含税)：</th>
      <td><?php echo $array['amount']; ?></td>
    </tr>
    <tr>
      <th>税率：</th>
      <td><?php echo ($array['tax_rate']*100).'%'; ?></td>
      <th>现金：</th>
      <td><?php echo $total_pay_amount; ?></td>
      <th>供应商：</th>
      <td colspan="3"><?php echo $array['supplier_cname']; ?></td>
    </tr>
    <tr>
      <th>订单日期：</th>
      <td><?php echo $order_date; ?></td>
      <th>计划回厂时间：</th>
      <td><?php echo $array['plan_date']; ?></td>
      <th>备注：</th>
      <td colspan="3"><?php echo $array['order_remark']; ?></td>
    </tr>
  </table>
</div>
<?php } ?>
<?php
$sql_pay = "SELECT `db_cash_pay`.`payid`,`db_cash_pay`.`pay_date`,`db_cash_pay`.`pay_amount`,`db_cash_pay`.`employeeid`,`db_cash_pay`.`dotime`,`db_cash_pay`.`remark`,`db_employee`.`employee_name` FROM `db_cash_pay` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cash_pay`.`employeeid` WHERE `db_cash_pay`.`linkid` = '$listid' AND `data_type` = 'MC' ORDER BY `db_cash_pay`.`pay_date` DESC,`db_cash_pay`.`payid` DESC";
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
    <td><?php echo $payid; ?></td>
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
	  echo "<p class=\"tag\">系统提示：暂无支付记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>