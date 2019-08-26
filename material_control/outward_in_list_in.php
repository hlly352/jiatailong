<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
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
<title>采购管理-苏州嘉泰隆</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  $sql_order = "SELECT `db_outward_order`.`order_number`,`db_outward_order`.`order_date`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_outward_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_outward_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_outward_order`.`employeeid` WHERE `db_outward_order`.`orderid` = '$orderid' AND `db_outward_order`.`employeeid` = '$employeeid'";
  $result_order = $db->query($sql_order);
  if($result_order->num_rows){
	  $array_order = $result_order->fetch_assoc();
  ?>
  <h4>物料采购订单</h4>
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
$sql = "SELECT `db_outward_order_list`.`listid`,`db_outward_order_list`.`order_quantity`,`db_outward_order_list`.`unit_price`,`db_outward_order_list`.`amount`,`db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_material`.`complete_status`,`db_mould`.`mould_number`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code` FROM `db_outward_order_list` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_outward_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` WHERE `db_outward_order_list`.`orderid` = '$orderid' $sqlwhere ORDER BY `db_mould`.`mould_number` DESC,`db_mould_material`.`materialid` ASC";
$result = $db->query($sql);
$result_id = $db->query($sql);
?>
<div id="table_list">
  <?php
  if($result->num_rows){
 ?>
  <form action="mould_outward_orderdo.php" name="material_order_list" method="post">
    <table>
      <tr>
        <th width="">ID</th>
        <th width="">模具编号</th>
        <th width="">料单编号</th>
        <th width="">料单序号</th>
        <th width="">物料编码</th>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th width="">材质</th>
        <th width="">数量</th>
        <th width="">加工数量</th>
        <th width="">单价</th>
        <th width="">金额</th>
      </tr>
      <?php
	  $amount = 0;
	  $process_cost = 0;
	  $total_amount = 0;
      while($row = $result->fetch_assoc()){
        $listid = $row['listid'];
	  ?>
      <tr>
        <td>
          <input type="checkbox" name="id[]" value="<?php echo $listid; ?>"<?php //if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> />
        </td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_list_number']; ?></td>
        <td><?php echo $row['material_list_sn']; ?></td>
        <td><?php echo $row['material_number']; ?></td>
        <td<?php echo $material_name_bg; ?>><?php echo $row['material_name']; ?></td>
        <td<?php echo $specification_bg; ?>><?php echo $row['specification'] ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['material_quantity']; ?></td>
        <td><?php echo $row['order_quantity'] ?></td>
        <td><?php echo $row['unit_price'] ?></td>
        <td><?php echo $row['amount'] ?></td>
      </tr>
      <?php
      $total_order_quantity += $row['order_quantity'];
      $total_amount += $row['amount'];
	  }
	  ?>
      <tr>
        <td colspan="9">Total</td>
        <td><?php echo number_format($total_order_quantity,2); ?></td>
        <td></td>
        <td><?php echo number_format($total_amount,2); ?></td>
      </tr>
      <tr>
        <td colspan="12">
          <input type="button" onclick="window.location.href='outward_in_list_indo.php?action=querys&orderid=<?php echo $orderid ?>'" value="确定" class="button" />
          <input type="button" onclick="window.location.href='outward_in_list.php'" value="返回" class="button" />
        </td>
      </tr>
    </table>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无物料记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>