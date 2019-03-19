<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
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
$sql = "SELECT `db_material_order_list`.`listid`,`db_material_order_list`.`order_quantity`,`db_material_order_list`.`actual_quantity`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`tax_rate`,`db_material_order_list`.`process_cost`,ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2) AS `amount`,(`db_material_order_list`.`process_cost`+ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2)) AS `total_amount`,ROUND(IF(SUM(`db_cash_pay`.`pay_amount`),SUM(`db_cash_pay`.`pay_amount`),0),2) AS `total_pay_amount`,((`db_material_order_list`.`process_cost`+ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2))-IF(SUM(`db_cash_pay`.`pay_amount`),SUM(`db_cash_pay`.`pay_amount`),0)) AS `wait_pay_amount`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name`,`db_unit_actual`.`unit_name` AS `actual_unit_name` FROM `db_material_order_list` LEFT JOIN `db_cash_pay` ON (`db_cash_pay`.`linkid` = `db_material_order_list`.`listid` AND `db_cash_pay`.`data_type` = 'M') INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_order_list`.`iscash` = 1 $sqlwhere GROUP BY `db_material_order_list`.`listid` HAVING ((`db_material_order_list`.`process_cost`+ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2)) > `total_pay_amount`)";
$result = $db->query($sql);
$result_allid = $db->query($sql);
$_SESSION['wait_material_order_payment_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_order`.`order_date` DESC,`db_material_order_list`.`listid` DESC" . $pages->limitsql;
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
  <h4>物料待付订单</h4>
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
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_wait_material_order_payment_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_allid = $result_allid->fetch_assoc()){
		  $all_total_amount += $row_allid['total_amount'];
		  $array_allid .= $row_allid['listid'].',';
	  }
	  $array_allid = rtrim($array_allid,',');
	  $sql_all_pay_amount = "SELECT `linkid`,SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` IN ($array_allid) AND `data_type` = 'M' GROUP BY `linkid`";
	  $result_all_pay_amount = $db->query($sql_all_pay_amount);
	  if($result_all_pay_amount->num_rows){
		  while($row_all_pay_amount = $result_all_pay_amount->fetch_assoc()){
			  $array_all_pay_amount[$row_all_pay_amount['linkid']] = $row_all_pay_amount['total_pay_amount'];
		  }
	  }else{
		  $array_all_pay_amount = array();
	  }
	  //print_r($array_all_pay_amount);
  ?>
  <table>
    <tr>
      <th width="4%" rowspan="2">ID</th>
      <th width="6%" rowspan="2">合同号</th>
      <th width="5%" rowspan="2">模具编号</th>
      <th width="8%" rowspan="2">物料名称</th>
      <th width="12%" rowspan="2">规格</th>
      <th colspan="2">需求</th>
      <th colspan="2">实际</th>
      <th width="5%" rowspan="2">单价<br />
        (含税)</th>
      <th width="4%" rowspan="2">税率</th>
      <th width="5%" rowspan="2">金额<br />
        (含税)</th>
      <th width="5%" rowspan="2">加工费</th>
      <th width="6%" rowspan="2">合计</th>
      <th width="5%" rowspan="2">已付</th>
      <th width="5%" rowspan="2">待付</th>
      <th width="6%" rowspan="2">供应商</th>
      <th width="6%" rowspan="2">订单日期</th>
      <th width="4%" rowspan="2">Pay</th>
    </tr>
    <tr>
      <th width="4%">数量</th>
      <th width="3%">单位</th>
      <th width="4%">数量</th>
      <th width="3%">单位</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
	   $listid = $row['listid'];
	?>
    <tr>
      <td><?php echo $listid; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $row['order_quantity']; ?></td>
      <td><?php echo $row['unit_name']; ?></td>
      <td><?php echo $row['actual_quantity']; ?></td>
      <td><?php echo $row['actual_unit_name']; ?></td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['tax_rate']*100; ?>%</td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $row['process_cost']; ?></td>
      <td><?php echo $row['total_amount']; ?></td>
      <td><?php echo $row['total_pay_amount']; ?></td>
      <td><?php echo $row['wait_pay_amount']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['order_date']; ?></td>
      <td><a href="pay_material_order_list.php?id=<?php echo $listid; ?>&amp;action=add"><img src="../images/system_ico/pay_12_12.png" width="12" height="12" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="13">Total</td>
      <td><?php echo number_format($all_total_amount,2); ?></td>
      <td><?php echo number_format(array_sum($array_all_pay_amount),2); ?></td>
      <td><?php echo number_format(($all_total_amount-array_sum($array_all_pay_amount)),2); ?></td>
      <td colspan="3">&nbsp;</td>
    </tr>
  </table>
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