<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
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
$sql = "SELECT `db_material_order_list`.`actual_quantity`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`tax_rate`,`db_material_order_list`.`process_cost`,ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2) AS `amount`,(`db_material_order_list`.`process_cost`+ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2)) AS `total_amount` ,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name`,`db_cash_pay`.`payid`,`db_cash_pay`.`pay_amount`,`db_cash_pay`.`pay_date`,`db_cash_pay`.`employeeid`,`db_employee`.`employee_name` FROM `db_cash_pay` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_cash_pay`.`linkid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid`= `db_material_order_list`.`actual_unitid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cash_pay`.`employeeid` WHERE (`db_cash_pay`.`pay_date` BETWEEN '$sdate' AND '$edate') AND `db_cash_pay`.`data_type` = 'M' $sqlwhere";
$result = $db->query($sql);
$result_total = $db->query($sql);
$_SESSION['material_order_payment_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cash_pay`.`pay_date` DESC,`db_cash_pay`.`payid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
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
<title>财务管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>物料已付订单</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" size="15" /></td>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" size="15" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" size="15" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" size="15" /></td>
        <th>付款日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="15" /></td>
        <th>供应商：</th>
        <td><select name="supplierid">
            <option value="">所有</option>
            <?php
            if($result_supplier->num_rows){
				while($row_supplier = $result_supplier->fetch_assoc()){
					echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_order_payment_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_total = $result_total->fetch_assoc()){
		  $total_pay_amount += $row_total['pay_amount'];
	  }
  ?>
  <form action="pay_listdo.php" name="material_pay_list" method="post">
    <table>
      <tr>
        <th width="4%">ID</th>
        <th width="6%">合同号</th>
        <th width="5%">模具编号</th>
        <th width="8%">物料名称</th>
        <th width="12%">规格</th>
        <th width="4%">数量</th>
        <th width="3%">单位</th>
        <th width="5%">单价<br />
          (含税)</th>
        <th width="3%">税率</th>
        <th width="5%">金额<br />
          (含税)</th>
        <th width="5%">加工费</th>
        <th width="5%">合计</th>
        <th width="5%">付款现金</th>
        <th width="6%">付款日期</th>
        <th width="4%">付款人</th>
        <th width="6%">供应商</th>
        <th width="6%">订单日期</th>
        <th width="4%">Edit</th>
        <th width="4%">Info</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $payid = $row['payid'];
	  ?>
      <tr>
        <td><input type="checkbox" name="id[]" value="<?php echo $payid; ?>"<?php if($row['employeeid'] != $employeeid) echo " disabled=\"disabled\""; ?> /></td>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $row['actual_quantity']; ?></td>
        <td><?php echo $row['unit_name']; ?></td>
        <td><?php echo $row['unit_price']; ?></td>
        <td><?php echo $row['tax_rate']*100; ?>%</td>
        <td><?php echo $row['amount']; ?></td>
        <td><?php echo $row['process_cost']; ?></td>
        <td><?php echo $row['total_amount']?></td>
        <td><?php echo $row['pay_amount']; ?></td>
        <td><?php echo $row['pay_date']; ?></td>
        <td><?php echo $row['employee_name']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
        <td><?php echo $row['order_date']; ?></td>
        <td><?php if($row['employeeid'] == $employeeid){ ?><a href="pay_material_order_list.php?id=<?php echo $payid; ?>&amp;action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
        <td><a href="material_order_payment_list_info.php?id=<?php echo $payid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
      </tr>
      <?php } ?>
      <tr>
      <td colspan="12">Total</td>
      <td><?php echo number_format($total_pay_amount,2); ?></td>
      <td colspan="6">&nbsp;</td>
      </tr>
    </table>
    <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="删除" class="select_button" onclick="JavaScript:return confirm('系统提示:确定删除吗?')" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
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