<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$payid = fun_check_int($_GET['id']);
$sql = "SELECT `db_cash_pay`.`pay_date`,`db_cash_pay`.`pay_amount`,`db_cash_pay`.`linkid`,`db_cash_pay`.`remark` AS `pay_remark`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`cost`,`db_mould_outward`.`iscash`,`db_mould_outward`.`applyer`,`db_mould_outward`.`plan_date`,`db_mould_outward`.`actual_date`,`db_mould_outward`.`inout_status`,`db_mould_outward`.`outward_status`,`db_mould_outward`.`remark` AS `outward_remark`,`db_mould_outward`.`dotime`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_mould_outward_type`.`outward_typename`,`db_employee`.`employee_name` FROM `db_cash_pay` INNER JOIN `db_mould_outward` ON `db_mould_outward`.`outwardid` = `db_cash_pay`.`linkid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_outward`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_outward`.`workteamid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_mould_outward`.`outward_typeid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_outward`.`employeeid` WHERE `db_cash_pay`.`payid` = '$payid' AND `db_cash_pay`.`data_type` = 'MO'";
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
</div>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$linkid = $array['linkid'];
	$inout_status = $array['inout_status'];
	$actual_date = $inout_status?$array['actual_date']:'--';
?>
<div id="table_sheet">
  <h4>外协加工付款信息</h4>
  <table>
    <tr>
      <th width="10%">模具编号：</th>
      <td width="15%"><?php echo $array['mould_number']; ?></td>
      <th width="10%">零件编号：</th>
      <td width="15%"><?php echo $array['part_number']; ?></td>
      <th width="10%">外协时间：</th>
      <td width="15%"><?php echo $array['order_date']; ?></td>
      <th width="10%">申请组别：</th>
      <td width="15%"><?php echo $array['workteam_name']; ?></td>
    </tr>
    <tr>
      <th>外协单号：</th>
      <td><?php echo $array['order_number']; ?></td>
      <th>数量：</th>
      <td><?php echo $array['quantity']; ?></td>
      <th>供应商：</th>
      <td><?php echo $array['supplier_cname']; ?></td>
      <th>类型：</th>
      <td><?php echo $array['outward_typename']; ?></td>
    </tr>
    <tr>
      <th>金额：</th>
      <td><?php echo $array['cost']; ?></td>
      <th>申请人：</th>
      <td><?php echo $array['applyer']; ?></td>
      <th>计划回厂：</th>
      <td><?php echo $array['plan_date']; ?></td>
      <th>实际回厂：</th>
      <td><?php echo $actual_date; ?></td>
    </tr>
    <tr>
      <th>现金：</th>
      <td><?php echo $array_is_status[$array['iscash']]; ?></td>
      <th>进度状态：</th>
      <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
      <th>状态：</th>
      <td><?php echo $array_status[$array['outward_status']]; ?></td>
      <th>操作人：</th>
      <td><?php echo $array['employee_name']; ?></td>
    </tr>
    <tr>
      <th>操作时间：</th>
      <td><?php echo $array['dotime']; ?></td>
      <th>备注：</th>
      <td colspan="5"><?php echo $array['outward_remark']; ?></td>
    </tr>
  </table>
</div>
<?php } ?>
<?php
$sql_pay = "SELECT `db_cash_pay`.`payid`,`db_cash_pay`.`pay_date`,`db_cash_pay`.`pay_amount`,`db_cash_pay`.`employeeid`,`db_cash_pay`.`dotime`,`db_cash_pay`.`remark`,`db_employee`.`employee_name` FROM `db_cash_pay` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cash_pay`.`employeeid` WHERE `db_cash_pay`.`linkid` = '$linkid' AND `data_type` = 'MO' ORDER BY `db_cash_pay`.`pay_date` DESC,`db_cash_pay`.`payid` DESC";
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
    <?php while($row_pay = $result_pay->fetch_assoc()){ ?>
    <tr<?php echo ($row_pay['payid'] == $payid)?" style=\"background:#DCD9FD\"":''; ?>>
      <td><?php echo $row_pay['payid']; ?></td>
      <td><?php echo $row_pay['pay_date']; ?></td>
      <td><?php echo $row_pay['pay_amount']; ?></td>
      <td><?php echo $row_pay['employee_name']; ?></td>
      <td><?php echo $row_pay['dotime']; ?></td>
      <td><?php echo $row_pay['remark']; ?></td>
    </tr>
    <?php
	$all_pay_amount += $row_pay['pay_amount'];
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