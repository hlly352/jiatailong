<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['id']);
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
  $sql_order = "SELECT `db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_material_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_order`.`employeeid` WHERE `db_material_order`.`orderid` = '$orderid'";
  $result_order = $db->query($sql_order);
  if($result_order->num_rows){
	  $array_order = $result_order->fetch_assoc();
  ?>
  <h4>物料订单</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="15%"><?php echo $array_order['order_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo $array_order['order_date']; ?></td>
      <th width="10%">供应商：</th>
      <td width="15%"><?php echo $array_order['supplier_cname']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_order['employee_name']; ?></td>
    </tr>
  </table>
  <?php
  }else{
	  die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
  }
  ?>
</div>
<?php
if($_GET['submit']){
	$mould_number = trim($_GET['mould_number']);
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['specification']);
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
}
$sql = "SELECT `db_material_order_list`.`listid`,`db_material_order_list`.`order_quantity`,`db_material_order_list`.`actual_quantity`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_unit`.`unit_name`,`db_unit_actual`.`unit_name` AS `actual_unit_name` FROM `db_material_order_list` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid`= `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_order_list`.`orderid` = '$orderid' $sqlwhere ORDER BY `db_mould`.`mould_number` DESC,`db_mould_material`.`materialid` ASC";
$result = $db->query($sql);
?>
<div id="table_search">
  <h4>订单明细</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <td><input type="hidden" name="id" value="<?php echo $orderid; ?>" />
          <input type="submit" name="submit" value="查询" class="button" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){  ?>
  <table>
    <tr>
      <th width="4%" rowspan="2">ID</th>
      <th width="8%" rowspan="2">模具编号</th>
      <th width="14%" rowspan="2">物料名称</th>
      <th width="20%" rowspan="2">规格</th>
      <th width="12%" rowspan="2">材质</th>
      <th colspan="2">需求</th>
      <th colspan="2">实际</th>
      <th width="8%" rowspan="2">计划回厂日期</th>
      <th width="12%" rowspan="2">备注</th>
    </tr>
    <tr>
      <th width="6%">数量</th>
      <th width="5%">单位</th>
      <th width="6%">数量</th>
      <th width="5%">单位</th>
    </tr>
    <?php
	$amount = 0;
	$process_cost = 0;
	$total_amount = 0;
	while($row = $result->fetch_assoc()){
		$listid = $row['listid'];
	?>
    <tr>
      <td><?php echo $listid; ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $row['texture']; ?></td>
      <td><?php echo $row['order_quantity']; ?></td>
      <td><?php echo $row['unit_name']; ?></td>
      <td><?php echo $row['actual_quantity']; ?></td>
      <td><?php echo $row['actual_unit_name']; ?></td>
      <td><?php echo $row['plan_date']; ?></td>
      <td><?php echo $row['remark']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无物料记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>