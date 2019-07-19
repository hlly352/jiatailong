<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$cutterid = fun_check_int($_GET['id']);
$sql_cutter = "SELECT `db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness` FROM `db_mould_cutter` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` WHERE `db_mould_cutter`.`cutterid` = '$cutterid'";
$result_cutter = $db->query($sql_cutter);
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
<title>模具刀具-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result_cutter->num_rows){
	$array_cutter = $result_cutter->fetch_assoc();
?>
<div id="table_sheet">
  <h4>模具刀具</h4>
  <table>
    <tr>
      <th width="10%">类型：</th>
      <td width="15%"><?php echo $array_cutter['type']; ?></td>
      <th width="10%">规格：</th>
      <td width="15%"><?php echo $array_cutter['specification']; ?></td>
      <th width="10%">材质：</th>
      <td width="15%"><?php echo $array_cutter['texture']; ?></td>
      <th width="10%">硬度：</th>
      <td width="15%"><?php echo $array_cutter['hardness']; ?></td>
    </tr>
  </table>
</div>
<?php
$sql_order = "SELECT `db_cutter_order_list`.`listid`,`db_cutter_order_list`.`surplus`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname` FROM `db_cutter_order_list` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` WHERE `db_cutter_purchase_list`.`cutterid` = '$cutterid' AND `db_cutter_order_list`.`surplus` > 0 ORDER BY `db_cutter_order`.`orderid` DESC,`db_cutter_order_list`.`listid` ASC";
$result_order = $db->query($sql_order);
?>
<div id="table_list">
  <?php if($result_order->num_rows){ ?>
  <table>
    <caption>
    订单列表
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="20%">合同号</th>
      <th width="16%">品牌</th>
      <th width="10%">结余</th>
      <th width="10%">单位</th>
      <th width="20%">供应商</th>
      <th width="20%">订单日期</th>
    </tr>
    <?php while($row_order = $result_order->fetch_assoc()){ ?>
    <tr>
      <td><?php echo $row_order['listid']; ?></td>
      <td><?php echo $row_order['order_number']; ?></td>
      <td><?php echo $row_order['brand']; ?></td>
      <td><?php echo $row_order['surplus']; ?></td>
      <td>件</td>
      <td><?php echo $row_order['supplier_cname']; ?></td>
      <td><?php echo $row_order['order_date']; ?></td>
    </tr>
    <?php
	$total_surplus += $row_order['surplus'];
	}
	?>
    <tr>
      <td colspan="3">Total</td>
      <td><?php echo $total_surplus; ?></td>
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无有结余订单记录</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>