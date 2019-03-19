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
  $sql_order = "SELECT `db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_material_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_order`.`employeeid` WHERE `db_material_order`.`orderid` = '$orderid' AND `db_material_order`.`employeeid` = '$employeeid'";
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
$sql = "SELECT `db_material_order_list`.`listid`,`db_material_order_list`.`order_quantity`,`db_material_order_list`.`actual_quantity`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`tax_rate`,`db_material_order_list`.`process_cost`,`db_material_order_list`.`iscash`,`db_material_order_list`.`plan_date`,ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2) AS `amount`,ROUND((`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`)+`db_material_order_list`.`process_cost`,2) AS `total_amount`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_unit`.`unit_name`,`db_unit_actual`.`unit_name` AS `actual_unit_name` FROM `db_material_order_list` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid`= `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_order_list`.`orderid` = '$orderid' $sqlwhere ORDER BY `db_mould`.`mould_number` DESC,`db_mould_material`.`materialid` ASC";
$result = $db->query($sql);
$result_id = $db->query($sql);
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
          <input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_order.php?id=<?php echo $orderid; ?>'" />
          <input type="button" name="button" value="添加" class="button" onclick="location.href='material_order_list_add.php?id=<?php echo $orderid; ?>'" /></td>
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
	  $sql_material_inout = "SELECT `listid` FROM `db_material_inout` WHERE `listid` IN ($array_listid) GROUP BY `listid`";
	  $result_material_inout = $db->query($sql_material_inout);
	  if($result_material_inout->num_rows){
		  while($row_material_inout = $result_material_inout->fetch_assoc()){
			  $array_material_inout[] = $row_material_inout['listid'];
		  }
	  }else{
		  $array_material_inout = array();
	  }
	  //查询是否有付款记录
	  $sql_pay_list = "SELECT `listid` FROM `db_cash_pay` WHERE `listid` IN ($array_listid) AND `data_type` = 'M' GROUP BY `listid`";
	  $result_pay = $db->query($sql_pay_list);
	  if($result_pay->num_rows){
		  while($row_pay = $result_pay->fetch_assoc()){
			  $array_pay[] = $row_pay['listid'];
		  }
	  }else{
		  $array_pay = array();
	  }
	  $array_list = array_unique(array_merge($array_material_inout,$array_pay));
	  //入库数量
	  $sql_material_in = "SELECT SUM(`inout_quantity`) AS `in_quantity`,SUM(`quantity`) AS `quantity`,`listid` FROM `db_material_inout` WHERE `db_material_inout`.`dotype` = 'I' AND `listid` IN ($array_listid) GROUP BY `listid`";
	  $result_material_in = $db->query($sql_material_in);
	  if($result_material_in->num_rows){
		  while($row_material_in = $result_material_in->fetch_assoc()){
			  $array_material_in[$row_material_in['listid']] = array('in_quantity'=>$row_material_in['in_quantity'],'quantity'=>$row_material_in['quantity']);
		  }
	  }else{
		  $array_material_in = array();
	  }
	  //print_r($array_material_in);
  ?>
  <form action="material_order_listdo.php" name="material_order_list" method="post">
    <table>
      <tr>
        <th width="4%" rowspan="2">ID</th>
        <th width="6%" rowspan="2">模具编号</th>
        <th width="10%" rowspan="2">物料名称</th>
        <th width="12%" rowspan="2">规格</th>
        <th width="6%" rowspan="2">材质</th>
        <th colspan="2">需求</th>
        <th colspan="3">实际</th>
        <th width="5%" rowspan="2">单价<br />
          (含税)</th>
        <th width="4%" rowspan="2">税率</th>
        <th width="5%" rowspan="2">金额<br />
          (含税)</th>
        <th width="5%" rowspan="2">加工费</th>
        <th width="6%" rowspan="2">合计</th>
        <th width="3%" rowspan="2">现金</th>
        <th width="6%" rowspan="2">计划回厂日期</th>
        <th width="4%" rowspan="2">Edit</th>
        <th width="4%" rowspan="2">Info</th>
      </tr>
      <tr>
        <th width="4%">数量</th>
        <th width="4%">单位</th>
        <th width="4%">数量</th>
        <th width="4%">入库</th>
        <th width="4%">单位</th>
      </tr>
      <?php
	  $amount = 0;
	  $process_cost = 0;
	  $total_amount = 0;
      while($row = $result->fetch_assoc()){
		  $listid = $row['listid'];
		  $in_quantity = array_key_exists($listid,$array_material_in)?$array_material_in[$listid]['in_quantity']:0;
		  $quantity = array_key_exists($listid,$array_material_in)?$array_material_in[$listid]['quantity']:0;
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $listid; ?>"<?php if(in_array($listid,$array_list)) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['order_quantity']; ?></td>
        <td><?php echo $row['unit_name']; ?></td>
        <td><?php echo $row['actual_quantity']; ?></td>
        <td title="<?php echo $quantity.$row['unit_name']; ?>"><?php echo $in_quantity; ?></td>
        <td><?php echo $row['actual_unit_name']; ?></td>
        <td><?php echo $row['unit_price']; ?></td>
        <td><?php echo $row['tax_rate']*100; ?>%</td>
        <td><?php echo $row['amount']; ?></td>
        <td><?php echo $row['process_cost']; ?></td>
        <td><?php echo $row['total_amount']; ?></td>
        <td><?php echo $array_is_status[$row['iscash']]; ?></td>
        <td><?php echo $row['plan_date']; ?></td>
        <td><?php if(!in_array($listid,$array_list)){ ?>
          <a href="material_order_list_edit.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a>
          <?php } ?></td>
        <td><a href="material_order_list_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
      </tr>
      <?php
      $amount += $row['amount'];
	  $process_cost += $row['process_cost'];
	  $total_amount += $row['total_amount'];
	  }
	  ?>
      <tr>
        <td colspan="12">Total</td>
        <td><?php echo number_format($amount,2); ?></td>
        <td><?php echo number_format($process_cost,2); ?></td>
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
	  echo "<p class=\"tag\">系统提示：暂无物料记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>