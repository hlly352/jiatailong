<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
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
	$iscash = $_GET['iscash'];
	if($iscash != NULL){
		$sql_iscash = " AND `db_material_order_list`.`iscash` = '$iscash'";
	}
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' AND `db_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid $sql_iscash";
}
$sql = "SELECT `db_material_order_list`.`listid`,`db_material_order_list`.`order_quantity`,`db_material_order_list`.`actual_quantity`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`tax_rate`,`db_material_order_list`.`process_cost`,`db_material_order_list`.`iscash`,`db_material_order_list`.`plan_date`,ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2) AS `amount`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name`,`db_unit_actual`.`unit_name` AS `actual_unit_name` FROM `db_material_order_list` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE (`db_material_order`.`order_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$result = $db->query($sql);
$result_total = $db->query($sql);
$_SESSION['order_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_order`.`order_date` DESC,`db_material_order_list`.`listid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
$result_id = $db->query($sql);
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
  <h4>物料订单明细</h4>
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
					echo "<option value=\"".$row_supplier['supplierid']."\">".$row_supplier['supplier_code'].'-'.$row_supplier['supplier_cname']."</option>";
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
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_order_list.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_id = $result_id->fetch_assoc()){
		  $array_id .= $row_id['listid'].',';
	  }
	  $array_id = rtrim($array_id,',');
	  $sql_pay_amount = "SELECT `linkid`,SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `data_type` = 'M' AND `linkid` IN ($array_id) GROUP BY `listid`";
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
		  $total_process_cost += $row_total['process_cost'];
	  }
  ?>
  <table>
    <tr>
      <th width="4%" rowspan="2">ID</th>
      <th width="6%" rowspan="2">合同号</th>
      <th width="5%" rowspan="2">模具编号</th>
      <th width="8%" rowspan="2">物料名称</th>
      <th width="12%" rowspan="2">规格</th>
      <th width="6%" rowspan="2">材质</th>
      <th colspan="2">需求</th>
      <th colspan="2">实际</th>
      <th width="5%" rowspan="2">单价<br />
        (含税)</th>
      <th width="3%" rowspan="2">税率</th>
      <th width="5%" rowspan="2">金额<br />
        (含税)</th>
      <th width="5%" rowspan="2">加工费</th>
      <th width="5%" rowspan="2">现金</th>
      <th width="6%" rowspan="2">供应商</th>
      <th width="6%" rowspan="2">订单日期</th>
      <th width="6%" rowspan="2">计划回厂时间</th>
      <th width="4%" rowspan="2">Info</th>
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
		$iscash = $row['iscash'];
		$pay_amount = ($iscash)?array_key_exists($listid,$array_pay_amount)?$array_pay_amount[$listid]:0:'--';
	?>
    <tr>
      <td><?php echo $listid; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['specification']; ?></td>
      <td><?php echo $row['texture']; ?></td>
      <td><?php echo $row['order_quantity']; ?></td>
      <td><?php echo $row['unit_name']; ?></td>
      <td><?php echo $row['actual_quantity']; ?></td>
      <td><?php echo $row['actual_unit_name']; ?></td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['tax_rate']*100; ?>%</td>
      <td><?php echo $row['amount']; ?></td>
      <td><?php echo $row['process_cost']; ?></td>
      <td><?php echo $pay_amount; ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['order_date']; ?></td>
      <td><?php echo $row['plan_date']; ?></td>
      <td><a href="material_order_list_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="12">Total</td>
      <td><?php echo number_format($total_amount,2); ?></td>
      <td><?php echo number_format($total_process_cost,2); ?></td>
      <td colspan="5">&nbsp;</td>
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