<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sql_cutter_type = "SELECT `typeid`,`type` FROM `db_cutter_type` ORDER BY `typeid` ASC";
$result_cutter_type = $db->query($sql_cutter_type);
//查询供应商
$sql_supplier = "SELECT `supplierid`,`supplier_code`,`supplier_cname` FROM `db_supplier` WHERE FIND_IN_SET(1,`supplier_typeid`) >0 ORDER BY `supplier_code` ASC";
$result_supplier = $db->query($sql_supplier);
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
$sql = "SELECT `db_cutter_order_list`.`listid`,`db_cutter_order_list`.`unit_price`,`db_cutter_order_list`.`tax_rate`,`db_cutter_purchase_list`.`quantity`,ROUND(`db_cutter_purchase_list`.`quantity`*`db_cutter_order_list`.`unit_price`,2) AS `amount`,ROUND(IF(SUM(`db_cash_pay`.`pay_amount`),SUM(`db_cash_pay`.`pay_amount`),0),2) AS `total_pay_amount`,((ROUND(`db_cutter_purchase_list`.`quantity`*`db_cutter_order_list`.`unit_price`,2))-IF(SUM(`db_cash_pay`.`pay_amount`),SUM(`db_cash_pay`.`pay_amount`),0)) AS `wait_pay_amount`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname` FROM `db_cutter_order_list` LEFT JOIN `db_cash_pay` ON (`db_cash_pay`.`linkid` = `db_cutter_order_list`.`listid` AND `db_cash_pay`.`data_type` = 'MC') INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` WHERE `db_cutter_order_list`.`iscash` = 1 $sqlwhere GROUP BY `db_cutter_order_list`.`listid` HAVING ((ROUND(`db_cutter_purchase_list`.`quantity`*`db_cutter_order_list`.`unit_price`,2)) > `total_pay_amount`)";
$result = $db->query($sql);
$result_allid = $db->query($sql);
$_SESSION['wait_cutter_order_payment_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_order`.`order_date` DESC,`db_cutter_order_list`.`listid` DESC" . $pages->limitsql;
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
  <h4>刀具待付订单</h4>
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
            <option value="<?php echo $row_supplier['supplierid']; ?>"<?php if($row_supplier['supplierid'] == $supplierid) echo " selected=\"selected\""; ?>><?php echo $row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']; ?></option>
            <?php
				}
			}
			?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_wait_cutter_order_payment_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_allid = $result_allid->fetch_assoc()){
		  $all_amount += $row_allid['amount'];
		  $array_allid .= $row_allid['listid'].',';
	  }
	  $array_allid = rtrim($array_allid,',');
	  $sql_all_pay_amount = "SELECT `linkid`,SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` IN ($array_allid) AND `data_type` = 'MC' GROUP BY `linkid`";
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
      <th width="4%">ID</th>
      <th width="6%">合同号</th>
      <th width="5%">类型</th>
      <th width="12%">规格</th>
      <th width="6%">材质</th>
      <th width="8%">硬度</th>
      <th width="6%">品牌</th>
      <th width="5%">数量</th>
      <th width="4%">单位</th>
      <th width="6%">单价<br />
        (含税)</th>
      <th width="4%">税率</th>
      <th width="6%">金额<br />
        (含税)</th>
      <th width="6%">已付</th>
      <th width="6%">待付</th>
      <th width="6%">供应商</th>
      <th width="6%">订单日期</th>
      <th width="4%">Pay</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
	   $listid = $row['listid'];
	?>
    <tr>
      <td><?php echo $listid; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['type']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
      <td><?php echo $row['hardness']; ?></td>
      <td><?php echo $row['brand']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td>件</td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['tax_rate']*100; ?>%</td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $row['total_pay_amount']; ?></td>
      <td><?php echo $row['wait_pay_amount']; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['order_date']; ?></td>
      <td><a href="pay_cutter_order_list.php?id=<?php echo $listid; ?>&amp;action=add"><img src="../images/system_ico/pay_12_12.png" width="12" height="12" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="11">Total</td>
      <td><?php echo number_format($all_amount,2); ?></td>
      <td><?php echo number_format(array_sum($array_all_pay_amount),2); ?></td>
      <td><?php echo number_format(($all_amount-array_sum($array_all_pay_amount)),2); ?></td>
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