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
//$sql = "SELECT `db_other_material_orderlist`.`listid`,`db_other_material_orderlist`.`order_quantity`,`db_material_order_list`.`actual_quantity`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`tax_rate`,`db_material_order_list`.`process_cost`,`db_material_order_list`.`iscash`,`db_material_order_list`.`plan_date`,`db_material_order_list`.`remark`,ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2) AS `amount`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name`,`db_unit_actual`.`unit_name` AS `actual_unit_name` FROM `db_material_order_list` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE (`db_material_order`.`order_date` BETWEEN '$sdate' AND '$edate') $sqlwhere";
$sql = "SELECT * FROM `db_other_material_orderlist` INNER JOIN `db_mould_other_material` ON `db_other_material_orderlist`.`materialid` = `db_mould_other_material`.`mould_other_id` INNER JOIN `db_other_material_order` ON `db_other_material_orderlist`.`orderid` = `db_other_material_order`.`orderid` INNER JOIN `db_employee` ON `db_mould_other_material`.`applyer` = `db_employee`.`employeeid` INNER JOIN `db_department` ON `db_mould_other_material`.`apply_team` = `db_department`.`deptid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_other_material_order`.`supplierid` INNER JOIN `db_other_material_data` ON `db_mould_other_material`.`material_name` = `db_other_material_data`.`dataid`";

$result = $db->query($sql);
$result_total = $db->query($sql);
$_SESSION['material_orderlist'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_other_material_order`.`order_date` DESC" . $pages->limitsql;
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
<title>采购管理-希尔林</title>
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
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_orderlist.php'" /></td>
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
	  $sql_pay_amount = "SELECT `linkid`,SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` IN ($array_listid) AND `data_type` = 'M' GROUP BY `linkid`";
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
  ?>
  <table>
    <tr>
      <th>ID</th>
      <th>合同号</th>
      <th>物料名称</th>
      <th>规格</th>
      <th>需求数量</th>
      <th>实际数量</th>
      <th>单位</th>
      <th>申请人</th>
      <th>申请部门</th>
      <th>单价<br />
        (含税)</th>
      <th>税率</th>
      <th>金额<br />
        (含税)</th>
      <th>现金</th>
      <th>供应商</th>
      <th>订单日期</th>
      <th>计划<br />回厂时间</th>
      <th>备注</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		$listid = $row['listid'];
		$iscash = $row['iscash'] == '0'?'否':'是';
		$remark = $row['remark'];
    //计算金额
    $amount = number_format((floatval($row['unit_price']) * floatval($row['actual_quantity'])),2,'.','');
		if($remark){
			$specification = "<span title=\"".$remark."\" style=\"text-decoration:underline;\">".$row['specification']."</span>";
		}else{
			$specification = $row['specification'];
		}
		$in_quantity = array_key_exists($listid,$array_material_in)?$array_material_in[$listid]['in_quantity']:0;
		$quantity = array_key_exists($listid,$array_material_in)?$array_material_in[$listid]['quantity']:0;
		$pay_amount = ($iscash)?array_key_exists($listid,$array_pay_amount)?$array_pay_amount[$listid]:0:'--';
	?>
    <tr>
      <td><input type="checkbox" value="<?php echo $listid; ?>"></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['material_specification']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['actual_quantity']; ?></td>
      <td><?php echo $row['unit']; ?></td>
      <td><?php echo $row['employee_name']; ?></td>
      <td><?php echo $row['dept_name']; ?></td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['tax_rate']*100; ?>%</td>
      <td><?php echo $amount; ?></td>
      <td><?php echo $iscash ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo date('Y-m-d',strtotime($row['order_date'])); ?></td>
      <td><?php echo $row['plan_date']; ?></td>
      <td><?php echo $row['remark'] ?></td>
    </tr>
    <?php } ?>
 <!--    <tr>
      <td colspan="13">Total</td>
      <td><?php echo number_format($total_amount,2); ?></td>
      <td><?php echo number_format($total_process_cost,2); ?></td>
      <td colspan="6">&nbsp;</td>
    </tr> -->
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