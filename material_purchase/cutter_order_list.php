<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['orderid']);
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
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
$sql_order = "SELECT `db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_cutter_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_order`.`employeeid` WHERE `db_cutter_order`.`orderid` = '$orderid' AND `db_cutter_order`.`employeeid` = '$employeeid'";
$result_order = $db->query($sql_order);
if($result_order->num_rows){
	$array_order = $result_order->fetch_assoc();
	$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
	$result_cutter_type = $db->query($sql_cutter_type);
?>
<div id="table_sheet">
  <h4>刀具订单</h4>
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
$sql = "SELECT `db_cutter_order_list`.`listid`,`db_cutter_order_list`.`in_quantity`,`db_cutter_order_list`.`unit_price`,`db_cutter_order_list`.`tax_rate`,`db_cutter_order_list`.`iscash`,`db_cutter_order_list`.`plan_date`,`db_cutter_purchase_list`.`quantity`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_cutter_purchase`.`purchase_number`,(`db_cutter_purchase_list`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_cutter_order_list` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` WHERE `db_cutter_order_list`.`orderid` = '$orderid' AND `db_cutter_order`.`employeeid` = '$employeeid' $sqlwhere  ORDER BY `db_cutter_purchase`.`purchaseid` DESC,`db_cutter_purchase_list`.`purchase_listid` ASC";
$result = $db->query($sql);
$result_id = $db->query($sql);
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
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_cutter_order.php?orderid=<?php echo $orderid; ?>'" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='cutter_order_list_add.php?orderid=<?php echo $orderid; ?>'" />
          <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_listid .= $row_id['listid'].',';
	  }
	  $array_listid = rtrim($array_listid,',');
	  //订单明细是否有出入库
	  $sql_cutter_inout = "SELECT `listid` FROM `db_cutter_inout` WHERE `listid` IN ($array_listid) GROUP BY `listid`";
	  $result_cutter_inout = $db->query($sql_cutter_inout);
	  if($result_cutter_inout->num_rows){
		  while($row_cutter_inout = $result_cutter_inout->fetch_assoc()){
			  $array_cutter_inout[] = $row_cutter_inout['listid'];
		  }
	  }else{
		  $array_cutter_inout = array();
	  }
	  //查询是否有付款记录
	  $sql_pay_list = "SELECT `listid` FROM `db_cash_pay` WHERE `listid` IN ($array_listid) AND `data_type` = 'MO' GROUP BY `listid`";
	  $result_pay = $db->query($sql_pay_list);
	  if($result_pay->num_rows){
		  while($row_pay = $result_pay->fetch_assoc()){
			  $array_pay[] = $row_pay['listid'];
		  }
	  }else{
		  $array_pay = array();
	  }
	  $array_list = array_unique(array_merge($array_cutter_inout,$array_pay));  
  ?>
  <form action="cutter_order_listdo.php" name="cutter_order_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="7%">申购单号</th>
        <th width="7%">类型</th>
        <th width="12%">规格</th>
        <th width="6%">材质</th>
        <th width="8%">硬度</th>
        <th width="6%">品牌</th>
        <th width="5%">订单<br />
          数量</th>
        <th width="5%">入库<br />
          数量</th>
        <th width="4%">单位</th>
        <th width="6%">单价<br />
          (含税)</th>
        <th width="4%">税率</th>
        <th width="6%">金额<br />
          (含税)</th>
        <th width="4%">现金</th>
        <th width="8%">计划回厂日期</th>
        <th width="4%">Edit</th>
        <th width="4%">Info</th>
      </tr>
      <?php
	  $total_amount = 0;
      while($row = $result->fetch_assoc()){
		  $listid = $row['listid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $listid; ?>"<?php if(in_array($listid,$array_list)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['purchase_number']; ?></td>
        <td><?php echo $row['type']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
        <td><?php echo $row['hardness']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td><?php echo $row['in_quantity']; ?></td>
        <td>件</td>
        <td><?php echo $row['unit_price']; ?></td>
        <td><?php echo $row['tax_rate']*100; ?>%</td>
        <td><?php echo $row['amount']; ?></td>
        <td><?php echo $array_is_status[$row['iscash']]; ?></td>
        <td><?php echo $row['plan_date']; ?></td>
        <td><?php if(!in_array($listid,$array_list)){ ?>
          <a href="cutter_order_list_edit.php?id=<?php echo $listid ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
        <td><a href="cutter_order_list_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
      </tr>
      <?php
	  $total_amount += $row['amount'];
	  }
	  ?>
      <tr>
        <td colspan="12">Total</td>
        <td><?php echo number_format($total_amount,2); ?></td>
        <td colspan="4">&nbsp;</td>
      </tr>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
    </div>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无订单明细</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>