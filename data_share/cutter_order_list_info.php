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
  $sql_order = "SELECT `db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_cutter_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_order`.`employeeid` WHERE `db_cutter_order`.`orderid` = '$orderid'";
  $result_order = $db->query($sql_order);
  if($result_order->num_rows){
	  $array_order = $result_order->fetch_assoc();
	  $sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
	  $result_cutter_type = $db->query($sql_cutter_type);
  ?>
  <h4>刀具订单</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="15%"><?php echo $array_order['order_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo $array_order['order_date']; ?></td>
      <th width="10%">供应商：</th>
      <td width="15%"><?php echo $array_order['supplier_cname']; ?></td>
      <th width="10%">下单人：</th>
      <td width="15%"><?php echo $array_order['employee_name']; ?></td>
    </tr>
  </table>
  <?php } ?>
</div>
<?php
if($_GET['submit']){
	$purchase_number = trim($_GET['purchase_number']);
	$specification = trim($_GET['specification']);
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
	}
	$sqlwhere = " AND `db_cutter_purchase`.`purchase_number` LIKE '%$purchase_number%' AND `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid";
}
$sql = "SELECT `db_cutter_order_list`.`listid`,`db_cutter_order_list`.`in_quantity`,`db_cutter_order_list`.`tax_rate`,`db_cutter_order_list`.`iscash`,`db_cutter_order_list`.`plan_date`,`db_cutter_purchase_list`.`quantity`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_cutter_purchase`.`purchase_number` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` WHERE `db_cutter_order_list`.`orderid` = '$orderid' $sqlwhere";
$result = $db->query($sql);
?>
<div id="table_search">
  <h4>订单明细</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>申购单号：</th>
        <td><input type="text" name="purchase_number" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>类型：</th>
        <td><select name="typeid">
            <option value="">所有</option>
            <?php
			if($result_cutter_type->num_rows){
				while($row_cutter_type = $result_cutter_type->fetch_assoc()){
			?>
            <option value="<?php echo $row_cutter_type['typeid']; ?>"<?php if($row_cutter_type['typeid'] == $typeid) echo " selected=\"selected\""; ?>><?php echo $row_cutter_type['type']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" id="submit" value="查询" class="button" />
        <input type="hidden" name="id" value="<?php echo $orderid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="10%">申购单号</th>
        <th width="8%">类型</th>
        <th width="16%">规格</th>
        <th width="10%">材质</th>
        <th width="10%">硬度</th>
        <th width="8%">品牌</th>
        <th width="6%">订单数量</th>
        <th width="6%">入库数量</th>
        <th width="6%">单位</th>
        <th width="8%">现金</th>
        <th width="8%">计划回厂日期</th>
      </tr>
      <?php while($row = $result->fetch_assoc()){ ?>
      <tr>
        <td><?php echo $row['listid']; ?></td>
        <td><?php echo $row['purchase_number']; ?></td>
        <td><?php echo $row['type']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
        <td><?php echo $row['hardness']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td><?php echo $row['in_quantity']; ?></td>
        <td>件</td>
        <td><?php echo $array_is_status[$row['iscash']]; ?></td>
        <td><?php echo $row['plan_date']; ?></td>
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