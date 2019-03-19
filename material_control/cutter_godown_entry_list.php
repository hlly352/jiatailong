<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$entryid = fun_check_int($_GET['entryid']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
$sql_entry = "SELECT `db_godown_entry`.`entry_number`,`db_godown_entry`.`entry_date`,`db_godown_entry`.`dotime`,`db_employee`.`employee_name` FROM `db_godown_entry` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_godown_entry`.`employeeid` WHERE `db_godown_entry`.`entryid` = '$entryid' AND `db_godown_entry`.`dotype` = 'C' AND `db_godown_entry`.`employeeid` = '$employeeid'";
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
  <h4>刀具入库单</h4>
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
<div id="table_search">
  <h4>入库单明细</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" /></td>
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
        <input type="button" name="button" value="添加" class="button" onclick="location.href='cutter_godown_entry_list_add.php?entryid=<?php echo $entryid; ?>'" />
        <input type="button" name="button" value="打印" class="button" onclick="window.open('cutter_godown_entry_print.php?id=<?php echo $entryid; ?>')" />
        <input type="hidden" name="entryid" value="<?php echo $entryid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
if($_GET['submit']){
	$order_number = rtrim($_GET['order_number']);
	$specification = rtrim($_GET['specification']);
	$typeid = $_GET['typeid'];
	if($typeid){
		$sql_typeid = " AND `db_cutter_specification`.`typeid` = '$typeid'";
	}
	$supplierid = $_GET['supplierid'];
	if($supplierid){
		$sql_supplierid = " AND `db_cutter_order`.`supplierid` = '$supplierid'";
	}
	$sqlwhere = " AND `db_cutter_order`.`order_number` LIKE '%$order_number%' AND `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid $sql_supplierid";
}
$sql = "SELECT `db_godown_entry_list`.`listid`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`remark`,`db_cutter_inout`.`form_number`,`db_cutter_order_list`.`unit_price`,`db_cutter_order`.`order_number`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,(`db_cutter_inout`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_godown_entry_list` INNER JOIN `db_godown_entry` ON `db_godown_entry`.`entryid` = `db_godown_entry_list`.`entryid` INNER JOIN `db_cutter_inout` ON `db_cutter_inout`.`inoutid` = `db_godown_entry_list`.`inoutid` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` WHERE `db_godown_entry_list`.`entryid` = '$entryid' AND `db_godown_entry`.`dotype` = 'C' $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_godown_entry_list`.`listid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_list">
  <form action="cutter_godown_entry_listdo.php" name="cutter_godown_entry_list" method="post">
    <?php if($result->num_rows){ ?>
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">合同号</th>
        <th width="6%">类型</th>
        <th width="10%">规格</th>
        <th width="6%">材质</th>
        <th width="8%">硬度</th>
        <th width="6%">品牌</th>
        <th width="7%">送货单号</th>
        <th width="5%">数量</th>
        <th width="4%">单位</th>
        <th width="6%">单价(含税)</th>
        <th width="6%">金额(含税)</th>
        <th width="8%">供应商</th>
        <th width="6%">入库日期</th>
        <th width="12%">备注</th>
        <?php while($row = $result->fetch_assoc()){ ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $row['listid']; ?>" /></td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['type']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
        <td><?php echo $row['hardness']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['form_number']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td>件</td>
        <td><?php echo $row['unit_price']; ?></td>
        <td><?php echo $row['amount']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
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