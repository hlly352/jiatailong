<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$listid = fun_check_int($_GET['id']);
$sql = "SELECT * FROM `db_other_material_orderlist` INNER JOIN `db_other_material_order` ON `db_other_material_order`.`orderid` = `db_other_material_orderlist`.`orderid` INNER JOIN `db_other_supplier` ON `db_other_supplier`.`other_supplier_id` = `db_other_material_order`.`supplierid` INNER JOIN `db_mould_other_material` ON `db_mould_other_material`.`mould_other_id` = `db_other_material_orderlist`.`materialid` INNER JOIN `db_other_material_data` ON `db_mould_other_material`.`material_name` = `db_other_material_data`.`dataid` WHERE `db_other_material_orderlist`.`listid` = '$listid'";
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
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $sql_max = "SELECT MAX(`inoutid`) AS `inoutid` FROM `db_other_material_inout` WHERE `listid` = '$listid' GROUP BY `listid`";
	  $result_max = $db->query($sql_max);
	  if($result_max->num_rows){
		  $array_max = $result_max->fetch_assoc();
		  $max_inoutid = $array_max['inoutid'];
	  }
  ?>
  <h4>物料订单信息</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="23%"><?php echo $array['order_number']; ?></td>
      <th width="10%">物料名称：</th>
      <td width="23%"><?php echo $array['material_name']; ?></td>
      <th width="10%">规格：</th>
      <td width="23%"><?php echo $array['material_specification']; ?></td>
    </tr>
    <tr>
      <th>供应商：</th>
      <td><?php echo $array['supplier_cname']; ?></td>
      <th>入库数量：</th>
      <td><?php echo $array['actual_quantity'].$array['unit']; ?></td>
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
<!-- <div id="table_list">
  <form action="material_inout_listdo.php" name="material_inout_list" method="post">
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
        <td><input type="checkbox" name="id[]" value="<?php echo $inoutid; ?>"<?php if($inoutid != $max_inoutid) echo " disabled=\"disabled\""; ?> /></td>
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
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" />
    </div> -->
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>