<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$entryid = fun_check_int($_GET['entryid']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date("Y-m-d",strtotime("-15 day"));
$edate = $_GET['edate']?$_GET['edate']:date("Y-m-d",strtotime("+7 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
$sql_entry = "SELECT `db_outdown`.`entry_number`,`db_outdown`.`entry_date`,`db_outdown`.`dotime`,`db_employee`.`employee_name` FROM `db_outdown` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_outdown`.`employeeid` WHERE `db_outdown`.`entryid` = '$entryid' AND `db_outdown`.`dotype` = 'M' AND `db_outdown`.`employeeid` = '$employeeid'";
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
<script language="javascript" type="text/javascript" src="../js/My97DatePicker/WdatePicker.js" ></script>
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
  <h4>物料出库单</h4>
  <table>
    <tr>
      <th width="10%">出库单：</th>
      <td width="15%"><?php echo $array_entry['entry_number']; ?></td>
      <th width="10%">出库单日期：</th>
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
  <h4>可选出库单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" size="12" /></td>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" size="12" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" size="12" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" size="12" /></td>
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
        <th>出库日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" /></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
        <input type="button" name="button" value="明细" class="button" onclick="location.href='material_godown_entry_list.php?entryid=<?php echo $entryid; ?>'" />
        <input type="hidden" name="entryid" value="<?php echo $entryid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
if($_GET['submit']){
	$order_number = trim($_GET['order_number']);
	$mould_number = trim($_GET['mould_number']);
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['specification']);
	$supplierid = $_GET['supplierid'];
	if($supplierid){
		$sql_supplierid = " AND `db_material_order`.`supplierid` = '$supplierid'";
	}
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' AND `db_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid";
}
$sql = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`listid`,`db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`amount`,`db_material_inout`.`remark`,`db_material_order_list`.`unit_price`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit_order`.`unit_name` AS `unit_name_order`,`db_unit_actual`.`unit_name` AS `unit_name_actual` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_inout`.`dotype` = 'O' AND (`db_material_inout`.`dodate` BETWEEN '$sdate' AND '$edate') AND `db_material_inout`.`inoutid` NOT IN (SELECT `db_outdown_list`.`inoutid` FROM `db_outdown_list` INNER JOIN `db_outdown` ON `db_outdown`.`entryid` = `db_outdown_list`.`entryid` WHERE `db_outdown`.`dotype` = 'M' GROUP BY `db_outdown_list`.`inoutid`) $sqlwhere";
$result = $db->query($sql);
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_inout`.`inoutid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_list">
  <form action="material_outdown_list_adddo.php" name="material_godown_entry_list_add" method="post">
    <?php if($result->num_rows){ ?>
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">合同号</th>
        <th width="6%">模具编号</th>
        <th width="10%">物料名称</th>
        <th width="12%">规格</th>
        <th width="6%">材质</th>
        <th width="8%">表单号</th>
        <th width="7%">出库数量</th>
        <th width="7%">实际数量</th>
        <th width="7%">单价(含税)</th>
        <th width="7%">金额(含税)</th>
        <th width="6%">供应商</th>
        <th width="8%">出库日期</th>
        <th width="6%">备注</th>
        <?php while($row = $result->fetch_assoc()){ ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $row['inoutid']; ?>" /></td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['form_number']; ?></td>
        <td><?php echo $row['quantity'].$row['unit_name_order']; ?></td>
        <td><?php echo $row['inout_quantity'].$row['unit_name_actual']; ?></td>
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
      <input type="submit" name="submit" id="submit" value="添加" class="select_button" disabled="disabled" />
      <input type="hidden" name="entryid" value="<?php echo $entryid; ?>" />
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