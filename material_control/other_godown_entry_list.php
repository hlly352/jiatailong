<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$entryid = fun_check_int($_GET['entryid']);
$employeeid = $_SESSION['employee_info']['employeeid'];
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(4,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
$sql_entry = "SELECT `db_godown_entry`.`entry_number`,`db_godown_entry`.`entry_date`,`db_godown_entry`.`dotime`,`db_employee`.`employee_name` FROM `db_godown_entry` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_godown_entry`.`employeeid` WHERE `db_godown_entry`.`entryid` = '$entryid' AND `db_godown_entry`.`dotype` = 'O' AND `db_godown_entry`.`employeeid` = '$employeeid'";
$result_entry = $db->query($sql_entry);
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
  if($result_entry->num_rows){
    $array_entry = $result_entry->fetch_assoc();
  ?>
  <h4>物料入库单</h4>
  <table>
    <tr>
      <th width="10%">入库单：</th>
      <td width="15%"><?php echo $array_entry['entry_number']; ?></td>
      <th width="10%">入库单日期：</th>
      <td width="15%"><?php echo $array_entry['entry_date']; ?></td>
      <th width="10%">制单人：</th>
      <td width="15%"><?php echo $array_entry['employee_name']; ?></td>
      <th width="10%">时间：</th>
      <td width="15%"><?php echo $array_entry['dotime']; ?></td>
    </tr>
  </table>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录！</p></div>";
  }
  ?>
</div>
<?php
if($_GET['submit']){
  $order_number = trim($_GET['order_number']);
  $mould_number = trim($_GET['mould_number']);
  $material_name = trim($_GET['material_name']);
  $specification = trim($_GET['specification']);
  $supplierid = $_GET['supplierid'];
  if($supplierid){
    $sql_supplierid = " AND `db_other_material_order`.`supplierid` = '$supplierid'";
  }
  if($specification){
    $specification_str = "AND `db_other_material_specification`.`specification_name` LIKE '%$specification%'";
  }
  $sqlwhere = " AND (`db_other_material_specification`.`material_name` LIKE '%$material_name%' OR `db_other_material_data`.`material_name` LIKE '%$material_name%') AND `db_other_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid $specification_str";
}
$sql = "SELECT `db_godown_entry_list`.`listid`,`db_other_material_order`.`order_number`,`db_other_material_data`.`material_name` AS `data_name`,`db_other_material_specification`.`material_name`,`db_mould_other_material`.`unit` AS `material_unit`,`db_other_material_data`.`unit`,`db_other_material_inout`.`form_number`,`db_other_material_inout`.`inout_quantity`,(`db_other_material_inout`.`inout_quantity` * `db_other_material_orderlist`.`unit_price`) AS `amount`,`db_other_material_orderlist`.`unit_price`,`db_supplier`.`supplier_cname`,`db_other_material_inout`.`dodate`,`db_other_material_orderlist`.`remark`,`db_other_material_specification`.`specification_name` FROM `db_godown_entry_list` INNER JOIN `db_godown_entry` ON `db_godown_entry`.`entryid` = `db_godown_entry_list`.`entryid` INNER JOIN `db_other_material_inout` ON `db_other_material_inout`.`inoutid` = `db_godown_entry_list`.`inoutid` INNER JOIN `db_other_material_orderlist` ON `db_other_material_orderlist`.`listid` = `db_other_material_inout`.`listid` INNER JOIN `db_other_material_order` ON `db_other_material_order`.`orderid` = `db_other_material_orderlist`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_other_material_order`.`supplierid` INNER JOIN `db_mould_other_material` ON `db_mould_other_material`.`mould_other_id` = `db_other_material_orderlist`.`materialid` LEFT JOIN `db_other_material_specification` ON `db_mould_other_material`.`material_name` = `db_other_material_specification`.`specificationid` LEFT JOIN `db_other_material_data` ON `db_other_material_data`.`dataid` = `db_other_material_specification`.`materialid`  WHERE `db_godown_entry_list`.`entryid` = '$entryid' AND `db_godown_entry`.`dotype` = 'O' $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_godown_entry_list`.`listid` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>入库单明细</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
        while($row_supplier = $result_supplier->fetch_assoc()){
      ?>
            <option value="<?php echo $row_supplier['supplierid']; ?>"<?php if($row_supplier['supplierid'] == $supplierid) echo " selected=\"selected\"" ?>><?php echo $row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']; ?></option>
            <?php
        }
      }
      ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
        <input type="button" name="button" value="添加" class="button" onclick="location.href='other_godown_entry_list_add.php?entryid=<?php echo $entryid; ?>'" />
        <input type="button" name="button" value="打印" class="button" onclick="window.open('other_godown_entry_print.php?id=<?php echo $entryid; ?>')" />
        <input type="hidden" name="entryid" value="<?php echo $entryid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <form action="other_godown_entry_listdo.php" name="material_godown_entry_list" method="post">
    <?php if($result->num_rows){ ?>
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">合同号</th>
        <th width="10%">物料名称</th>
        <th width="12%">规格</th>
        <th width="7%">入库数量</th>
        <th width="7%">单价(含税)</th>
        <th width="7%">金额(含税)</th>
        <th width="6%">供应商</th>
        <th width="8%">表单号</th>
        <th width="8%">出入库日期</th>
        <th width="6%">备注</th>
        <?php while($row = $result->fetch_assoc()){ ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $row['listid']; ?>" /></td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['material_unit']?$row['material_name']:$row['data_name']; ?></td>
        <td><?php echo $row['specification_name']; ?></td>
        <td><?php echo $row['inout_quantity'] ?></td>
        <td><?php echo $row['unit_price']; ?></td>
        <td><?php echo $row['amount']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
        <td><?php echo $row['form_number']; ?></td>
        <td><?php echo $row['dodate']; ?></td>
        <td><?php echo $row['remark']; ?></td>
      </tr>
      <?php } ?>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
    </div>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
    echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>