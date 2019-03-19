<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$sdate = $_GET['sdate']?$_GET['sdate']:date('Y-m-01');
$edate = $_GET['edate']?$_GET['edate']:date('Y-m-d',strtotime($sdate."+1 month -1 day"));
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
	$iscash = $_GET['iscash'];
	if($iscash != NULL){
		$sql_iscash = " AND `db_cutter_order_list`.`iscash` = '$iscash'";
	}
	$sqlwhere = " AND `db_cutter_order`.`order_number` LIKE '%$order_number%' AND `db_cutter_specification`.`specification` LIKE '%$specification%' $sql_typeid $sql_supplierid $sql_iscash";
}
$sql = "SELECT `db_cutter_order_list`.`listid`,`db_cutter_order_list`.`in_quantity`,`db_cutter_order_list`.`unit_price`,`db_cutter_order_list`.`tax_rate`,`db_cutter_order_list`.`iscash`,`db_cutter_order_list`.`plan_date`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_cutter_purchase_list`.`quantity`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,(`db_cutter_purchase_list`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_cutter_order_list` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` WHERE (`db_cutter_order`.`order_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_total = $db->query($sql);
$_SESSION['cutter_orderlist'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_cutter_order`.`orderid` DESC,`db_cutter_order_list`.`listid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sqllist);
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
  <h4>刀具订单明细</h4>
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
        <th>订单日期：</th>
        <td><input type="text" name="sdate" value="<?php echo $sdate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" />
          --
          <input type="text" name="edate" value="<?php echo $edate; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" /></td>
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
        <th>现金：</th>
        <td><select name="iscash">
            <option value="">所有</option>
            <?php foreach($array_is_status as $is_status_key=>$is_statua_value){ ?>
            <option value="<?php echo $is_status_key; ?>"<?php if($is_status_key == $iscash && $iscash != NULL) echo " selected=\"selected\""; ?>><?php echo $is_statua_value; ?></option>
            <?php } ?>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_cutter_orderlist.php'" /></td>
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
	  //支付金额
	  $sql_pay_amount = "SELECT `linkid`,SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` IN ($array_listid) AND `data_type` = 'MC' GROUP BY `linkid`";
	  $result_pay_amount = $db->query($sql_pay_amount);
	  if($result_pay_amount->num_rows){
		  while($row_pay_amount = $result_pay_amount->fetch_assoc()){
			  $array_pay_amount[$row_pay_amount['linkid']] = $row_pay_amount['total_pay_amount'];
		  }
	  }else{
		  $array_pay_amount = array();
	  }
	  while($row_total = $result_total->fetch_assoc()){
		  $total_amount += $row_total['amount'];
	  }
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="6%">合同号</th>
      <th width="6%">类型</th>
      <th width="10%">规格</th>
      <th width="6%">材质</th>
      <th width="8%">硬度</th>
      <th width="6%">品牌</th>
      <th width="4%">订单<br />
        数量</th>
      <th width="4%">入库<br />
        数量</th>
      <th width="4%">单位</th>
      <th width="6%">单价(含税)</th>
      <th width="4%">税率</th>
      <th width="6%">金额(含税)</th>
      <th width="4%">现金</th>
      <th width="6%">供应商</th>
      <th width="6%">订单日期</th>
      <th width="6%">回厂计划日期</th>
      <th width="4%">Info</th>
    </tr>
    <?php
    while($row = $result->fetch_assoc()){
		$listid = $row['listid'];
		$iscash = $row['iscash'];
		$pay_amount = ($iscash)?array_key_exists($listid,$array_pay_amount)?$array_pay_amount[$listid]:0:'--';
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
      <td><?php echo $row['in_quantity']; ?></td>
      <td>件</td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['tax_rate']*100; ?>%</td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $pay_amount; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['order_date']; ?></td>
      <td><?php echo $row['plan_date']; ?></td>
      <td><a href="cutter_order_list_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="12">Total</td>
      <td><?php echo number_format($total_amount,2); ?></td>
      <td colspan="5">&nbsp;</td>
    </tr>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>