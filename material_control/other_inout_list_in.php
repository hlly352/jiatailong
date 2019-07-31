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
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' AND `db_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid";
}
$sql = "SELECT * FROM `db_other_material_inout` INNER JOIN `db_other_material_orderlist` ON `db_other_material_orderlist`.`listid` = `db_other_material_inout`.`listid` INNER JOIN `db_other_material_order` ON `db_other_material_order`.`orderid` = `db_other_material_orderlist`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_other_material_order`.`supplierid` INNER JOIN `db_mould_other_material` ON `db_mould_other_material`.`mould_other_id` = `db_other_material_orderlist`.`materialid` INNER JOIN `db_other_material_data` ON `db_mould_other_material`.`material_name` = `db_other_material_data`.`dataid`  WHERE `db_other_material_inout`.`dotype` = 'I' AND (`db_other_material_inout`.`dodate` BETWEEN '$sdate' AND '$edate') $sqlwhere";

$result = $db->query($sql);
$result_total = $db->query($sql);
$_SESSION['material_inout_list_in'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_other_material_inout`.`inoutid` DESC" . $pages->limitsql;

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
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>物料入库记录</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" size="15" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" size="15" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" size="15" /></td>
        <th>入库日期：</th>
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
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_inout_in.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_total = $result_total->fetch_assoc()){
		  $total_amount += $row_total['amount'];
		  $total_process_cost += $row_total['process_cost'];	
	  }																																				
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="6%">合同号</th>
      <th width="8%">物料名称</th>
      <th width="12%">规格</th>
      <th width="6%">表单号</th>
      <th width="6%">订单<br />
        数量</th>
      <th width="6%">实际<br />
        数量</th>
      <th width="5%">单位</th>
      <th width="6%">单价<br />
        (含税)</th>
      <th width="5%">金额<br />
        (含税)</th>
      <th width="5%">供应商</th>
      <th width="6%">入库日期</th>
   <!--    <th width="5%">Edit</th>
      <th width="5%">Info</th> -->
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		$inoutid = $row['inoutid'];
		$listid = $row['listid'];
	?>
    <tr>
      <td><?php echo $inoutid; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['material_specification']; ?></td>
      <td><?php echo $row['form_number']; ?></td>
      <td><?php echo $row['quantity']; ?></td>
      <td><?php echo $row['inout_quantity']; ?></td>
      <td><?php echo $row['unit']; ?></td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo $row['amounts'] ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['dodate']; ?></td>
      <!-- <td><a href="other_material_in_listin.php?id=<?php echo $inoutid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
      <td><a href="other_material_inout_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td> -->
    </tr>
    <?php } ?>
    <tr>
      <td colspan="9">Total</td>
      <td><?php echo number_format($total_amount,2); ?></td>
      <td></td>
      <td colspan="4">&nbsp;</td>
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