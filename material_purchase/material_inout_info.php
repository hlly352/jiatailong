<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$listid = fun_check_int($_GET['id']);
$sql = "SELECT `db_material_order_list`.`order_quantity`,`db_material_order_list`.`in_quantity`,`db_material_order_list`.`order_surplus`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name` FROM `db_material_order_list` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` WHERE `db_material_order_list`.`listid` = '$listid'";
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
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
  ?>
  <h4>物料订单信息</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="23%"><?php echo $array['order_number']; ?></td>
      <th width="10%">模具编号：</th>
      <td width="24%"><?php echo $array['mould_number']; ?></td>
      <th width="10%">物料名称：</th>
      <td width="23%"><?php echo $array['material_name']; ?></td>
    </tr>
    <tr>
      <th>规格：</th>
      <td><?php echo $array['specification']; ?></td>
      <th>材质：</th>
      <td><?php echo $array['texture']; ?></td>
      <th>供应商：</th>
      <td><?php echo $array['supplier_cname']; ?></td>
    </tr>
    <tr>
      <th>订单数量：</th>
      <td><?php echo $array['order_quantity'].$array['unit_name']; ?></td>
      <th>入库数量：</th>
      <td><?php echo $array['in_quantity'].$array['unit_name']; ?></td>
      <th>结余数量：</th>
      <td><?php echo $array['order_surplus'].$array['unit_name']; ?></td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录!</p>";
  }
  ?>
</div>
<?php
$sql_inout = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`form_number`,`db_material_inout`.`dodate`,`db_material_inout`.`dotype`,`db_material_inout`.`quantity`,IF(`db_material_inout`.`dotype` = 'I',`db_material_inout`.`inout_quantity`,'-') AS `inout_quantity`,`db_material_inout`.`remark`,`db_material_inout`.`dotime`,IF(`db_material_inout`.`dotype` = 'I',`db_material_order_list`.`unit_price`,'-') AS `unit_price`,IF(`db_material_inout`.`dotype` = 'I',`db_material_inout`.`amount`,'-') AS `amount`,`db_unit_order`.`unit_name` AS `unit_name_order`,IF(`db_material_inout`.`dotype` = 'I',`db_unit_actual`.`unit_name`,'') AS `unit_name_actual`,`db_employee`.`employee_name` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_inout`.`employeeid` WHERE `db_material_inout`.`listid` ='$listid' ORDER BY `db_material_inout`.`inoutid` DESC";
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
	$surplus +=$quantity;
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
<?php include "../footer.php"; ?>
</body>
</html>