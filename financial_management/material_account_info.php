<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$accountid = $_GET['id'];

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
//通过对账汇总表id查询详情
$sql = "SELECT `db_material_account_list`.`accountid`,`db_material_inout`.`inoutid`,`db_material_inout`.`listid`,`db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`amount`,`db_material_inout`.`process_cost`,`db_material_order_list`.`unit_price`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit_order`.`unit_name` AS `unit_name_order`,`db_unit_actual`.`unit_name` AS `unit_name_actual` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` INNER JOIN `db_material_account_list` ON `db_material_inout`.`inoutid` = `db_material_account_list`.`inoutid` WHERE `db_material_account_list`.`accountid`='$accountid' AND `db_material_inout`.`dotype` ='I' AND `db_material_inout`.`account_status` = 'F' $sqlwhere";
$sql = $sql.'ORDER BY `db_material_order`.`orderid`';
$result = $db->query($sql);
?>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
    <caption>
    对账详情
    </caption>
      <tr>
      <th width="4%">ID</th>
      <th width="8%">合同号</th>
      <th width="5%">模具编号</th>
      <th width="6%">物料名称</th>
      <th width="10%">规格</th>
      <th width="6%">材质</th>
      <th width="7%">表单号</th>
      <th width="4%">订单<br />
        数量</th>
      <th width="4%">单位</th>
      <th width="4%">实际<br />
        数量</th>
      <th width="4%">单位</th>
      <th width="5%">单价<br />
        (含税)</th>
      <th width="5%">金额<br />
        (含税)</th>
      <th width="4%">加工费</th>
      <th width="4%">核销</th>
      <th width="4%">品质<br />
      扣款</th>
      <th width="6%">供应商</th>
      <th width="6%">入库日期</th>
    </tr>
    <?php
	$surplus = 0;
	while($row = $result->fetch_assoc()){
		$inoutid = $row['inoutid'];
    $accountid = $row['accountid'];
		$dotype = $row['dotype'];
		$quantity = ($dotype == 'I')?$row['quantity']:(-$row['quantity']);
	?>
  <tr>
      <td>
        <input type="checkbox" name="id[]" value="<?php echo $inoutid?>">
      </td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $row['texture']; ?></td>
      <td><?php echo $row['form_number']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['unit_name_order']; ?></td>
      <td><?php echo $row['inout_quantity']; ?></td>
      <td><?php echo $row['unit_name_actual']; ?></td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $row['process_cost']; ?></td>
      <td><?php echo $row['process_cost']; ?></td>
      <td><?php echo $row['process_cost']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['dodate']; ?></td>
    </tr>
    <?php
	$amount +=$row['amount'];
	}
	?>
    <tr>
      <td colspan="12">总金额</td>
      <td><?php echo number_format($amount,2,'.',''); ?></td>
      <td colspan="7">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="18">
        <input type="button" class="button" name="" value="确定" onclick="window.location.assign('material_balance_account_do.php?accountid=<?php echo $accountid ?>&action=complete')">
        &nbsp;
        <input type="button" class="button" name="" value="退回" onclick="window.location.assign('material_balance_account_do.php?accountid=<?php echo $accountid ?>&action=back')">
        &nbsp;
        <input type="button" class="button" onclick="window.history.go(-1)" value="返回">
      </td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无出入库记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>