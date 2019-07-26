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
// $sql = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`listid`,`db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`amount`,`db_material_inout`.`process_cost`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`plan_date`,DATEDIFF(`db_material_order_list`.`plan_date`,`db_material_inout`.`dodate`) AS `diff_date`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit_order`.`unit_name` AS `unit_name_order`,`db_unit_actual`.`unit_name` AS `unit_name_actual` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_inout`.`dotype` = 'I' AND (`db_material_inout`.`dodate` BETWEEN '$sdate' AND '$edate') AND DATEDIFF(`db_material_order_list`.`plan_date`,`db_material_inout`.`dodate`) < 0 $sqlwhere";

$sql = "SELECT `db_material_order_list`.`orderid` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_inout`.`dotype` = 'I' AND `db_material_order_list`.`actual_unitid` !='3'  AND (`db_material_inout`.`dodate` BETWEEN '$sdate' AND '$edate') AND (`db_material_order_list`.`order_quantity` - `db_material_inout`.`inout_quantity` - `db_material_inout`.`cancel_num`) > 0 $sqlwhere";
$result = $db->query($sql);
$_SESSION['material_abnormal_entry'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_material_inout`.`inoutid` DESC" . $pages->limitsql;
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
  <h4>物料异常入库</h4>
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
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_material_abnormal_in.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <table>
<tr>
      <th width="4%">ID</th>
      <th width="6%">合同号</th>
      <th width="6%">模具编号</th>
      <th width="7%">物料名称</th>
      <th width="10%">规格</th>
      <th width="6%">材质</th>
      <th width="5%">订单<br />
        数量</th>
      <th width="5%">入库<br />
        数量</th>
      <th width="5%">单价<br />
        (含税)</th>
      <th width="5%">金额<br />
        (含税)</th>
      <th width="5%">加工费</th>
      <th width="6%">供应商</th>
      <th width="6%">入库日期</th>
      <th width="7%">货运单号</th>
      <th width="5%">计划回厂日期</th>
      <th width="4%">差异</th>
      <th width="4%">核销单</th>
      <th width="4%">操作</th>
    </tr>
    <?php $row = $result->fetch_assoc();
      $order_sql = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`listid`,`db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`amount`,`db_material_inout`.`process_cost`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`plan_date`,IF(`db_material_order_list`.`actual_unitid`='3',0.00,`db_material_order_list`.`order_quantity`-`db_material_inout`.`inout_quantity`) AS `diff_date`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit_order`.`unit_name` AS `unit_name_order`,`db_unit_actual`.`unit_name` AS `unit_name_actual` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_order_list`.`orderid`=".$row['orderid'];
        $result_order = $db->query($order_sql);
        if($result_order->num_rows){
          while($rows = $result_order->fetch_assoc()){
      
     ?>
    <tr>
      <td><?php echo $rows['inoutid']; ?></td>
      <td><?php echo $rows['order_number']; ?></td>
      <td><?php echo $rows['mould_number']; ?></td>
      <td><?php echo $rows['material_name']; ?></td>
      <td><?php echo $rows['specification']; ?></td>
      <td><?php echo $rows['texture']; ?></td>
      <td><?php echo $rows['quantity'].$rows['unit_name_order']; ?></td>
      <td><?php echo $rows['inout_quantity'].$rows['unit_name_actual']; ?></td>
      <td><?php echo $rows['unit_price']; ?></td>
      <td><?php echo $rows['amount']; ?></td>
      <td><?php echo $rows['process_cost']; ?></td>
      <td><?php echo $rows['supplier_cname']; ?></td>
      <td><?php echo $rows['dodate']; ?></td>
      <td><?php echo $rows['form_number']; ?></td>
      <td><?php echo $rows['plan_date']; ?></td>
      <td><?php echo $rows['diff_date']; ?></td>
      <td>
      <?php
          if($rows['diff_date'] != 0){
            echo '<a href="material_cancel_order_print.php?listid='.$rows['listid'].'&diff='.$rows['diff_date'].'&inoutid='.$rows['inoutid'].'">打印</a>';
          }
        ?>
      </td>
      <td>
        <?php
          if($rows['diff_date'] != 0){
            echo '<a href="material_cancel_order.php?listid='.$rows['listid'].'&diff='.$rows['diff_date'].'&inoutid='.$rows['inoutid'].'">核销</a>';
          }
        ?>
        
      </td>
    </tr>
    <?php } ?>
  </table>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }}else{
	  echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>