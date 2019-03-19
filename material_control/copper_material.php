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
	$ratio = $_GET['ratio'];
	if($ratio == 'A'){
		$sql_ratio = " HAVING `amount_ratio` > 1.1";
	}elseif($ratio == 'B'){
		$sql_ratio = " HAVING `amount_ratio` < 0.95";
	}
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' AND `db_material_order`.`order_number` LIKE '%$order_number%' $sql_supplierid";
}
$sql = "SELECT `db_material_order_list`.`listid`,`db_material_order_list`.`order_quantity`,`db_material_order_list`.`unit_price`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_mould_material`.`material_name`,(`db_mould_material`.`specification`+0) AS `theoretical_quantity`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,SUM(`db_material_inout`.`inout_quantity`) AS `actual_quantity`,(SUM(`db_material_inout`.`inout_quantity`)*`db_material_order_list`.`unit_price`) AS `amount`,(SUM(`db_material_inout`.`inout_quantity`)-(`db_mould_material`.`specification`+0)) AS `diff_amount`,SUM(`db_material_inout`.`inout_quantity`)/(`db_mould_material`.`specification`+0) AS `amount_ratio` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` WHERE SUBSTRING(`db_mould_material`.`material_number`,1,1) = 9 AND `db_material_order_list`.`order_quantity` = `db_material_order_list`.`in_quantity` AND `db_material_inout`.`dotype` = 'I' AND (`db_material_order`.`order_date` BETWEEN '$sdate' AND '$edate') $sqlwhere GROUP BY `db_material_inout`.`listid` $sql_ratio";
$result = $db->query($sql);
$result_all = $db->query($sql);
$_SESSION['copper_material'] = $sql;
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
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_search">
  <h4>铜料分析</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>合同号：</th>
        <td><input type="text" name="order_number" class="input_txt" size="14" /></td>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" size="14" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" size="14" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" size="14" /></td>
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
        <th>占比：</th>
        <td><select name="ratio">
            <option value="">所有</option>
            <option value="A">大于1.1</option>
            <option value="B">小于0.95</option>
          </select></td>
        <td><input type="submit" name="submit" value="查询" class="button" />
          <input type="button" name="button" value="导出" class="button" onclick="location.href='excel_copper_material.php'" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
	  while($row_all = $result_all->fetch_assoc()){
		  $total_order_quantity += $row_all['order_quantity'];
		  $total_theoretical_quantity += $row_all['theoretical_quantity'];
		  $total_actual_quantity += $row_all['actual_quantity'];
		  $total_amount += $row_all['amount'];
		  $total_diff_amount += $row_all['diff_amount'];
		  $total_amount_ratio = $total_actual_quantity/$total_theoretical_quantity;
		  if($total_amount_ratio > 1.1){
			  $total_amount_ratio_bg = " style=\"background:red\"";
		  }elseif($total_amount_ratio < 0.95){
			  $total_amount_ratio_bg = " style=\"background:green\"";
		  }else{
			  $total_amount_ratio_bg = '';
		  }
	  }
  ?>
  <table>
    <tr>
      <th width="4%">ID</th>
      <th width="8%">合同号</th>
      <th width="8%">模具编号</th>
      <th width="8%">物料名称</th>
      <th width="6%">材质</th>
      <th width="6%">订单数量(个)</th>
      <th width="8%">理论重量(Kg)</th>
      <th width="8%">实际重量(Kg)</th>
      <th width="6%">单价(含税)</th>
      <th width="6%">金额(含税)</th>
      <th width="6%">差异数量</th>
      <th width="6%">占比</th>
      <th width="8%">供应商</th>
      <th width="8%">订单日期</th>
      <th width="4%">Info</th>
    </tr>
    <?php
	while($row = $result->fetch_assoc()){
		$listid = $row['listid'];
		$amount_ratio = $row['amount_ratio'];
		if($amount_ratio > 1.1){
			$amount_ratio_bg = " style=\"background:red\"";
		}elseif($amount_ratio < 0.95){
			$amount_ratio_bg = " style=\"background:green\"";
		}else{
			$amount_ratio_bg = '';
		}
	?>
    <tr>
      <td><?php echo $listid; ?></td>
      <td><?php echo $row['order_number']; ?></td>
      <td><?php echo $row['mould_number']; ?></td>
      <td><?php echo $row['material_name']; ?></td>
      <td><?php echo $row['texture']; ?></td>
      <td><?php echo $row['order_quantity']; ?><?php echo $row['unit_name']; ?></td>
      <td><?php echo number_format($row['theoretical_quantity'],2); ?><?php echo $row['actual_unit_name']; ?></td>
      <td><?php echo number_format($row['actual_quantity'],2); ?><?php echo $row['actual_unit_name']; ?></td>
      <td><?php echo $row['unit_price']; ?></td>
      <td><?php echo number_format($row['amount'],2); ?></td>
      <td><?php echo number_format($row['diff_amount'],2); ?></td>
      <td<?php echo $amount_ratio_bg; ?>><?php echo number_format($row['amount_ratio'],2); ?></td>
      <td><?php echo $row['supplier_cname']; ?></td>
      <td><?php echo $row['order_date']; ?></td>
      <td><a href="copper_material_info.php?id=<?php echo $listid; ?>"><img src="../images/system_ico/info_8_10.png" width="8" height="10" /></a></td>
    </tr>
    <?php } ?>
    <tr>
      <td colspan="5">Total</td>
      <td><?php echo number_format($total_order_quantity,2); ?></td>
      <td><?php echo number_format($total_theoretical_quantity,2); ?></td>
      <td><?php echo number_format($total_actual_quantity,2); ?></td>
      <td>&nbsp;</td>
      <td><?php echo number_format($total_amount,2); ?></td>
      <td><?php echo number_format($total_diff_amount,2); ?></td>
      <td<?php echo $total_amount_ratio_bg; ?>><?php echo number_format($total_amount_ratio,2); ?></td>
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