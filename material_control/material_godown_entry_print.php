<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$entryid = fun_check_int($_GET['id']);
$sql_entry = "SELECT `entry_number` FROM `db_godown_entry` WHERE `entryid` = '$entryid' AND `dotype` = 'M'";
$result_entry = $db->query($sql_entry);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
@charset "utf-8";
/*Base_css*/
body, html {
	height:100%;
}
* {
	margin:0;
	padding:0;
	font-family:"微软雅黑", "宋体";
}
#main {
	width:1040px;
	height:662px;
	margin:0 auto;
}
#sheet {
	border-collapse:collapse;
	width:100%;
	margin-top:20px;
}
#sheet th, #sheet td {
	border:1px solid #000;
	font-size:13px;
	text-align:center;
	padding:8px 0;
	word-break:break-all;
	word-wrap:break-all;
}
</style>
<title>物料入库单打印-希尔林</title>
</head>

<body>
<?php
if($result_entry->num_rows){
	$array_entry = $result_entry->fetch_assoc();
	$sql = "SELECT `db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`amount`,`db_material_inout`.`process_cost`,`db_material_inout`.`remark`,`db_material_order_list`.`unit_price`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name` FROM `db_godown_entry_list` INNER JOIN `db_godown_entry` ON `db_godown_entry`.`entryid` = `db_godown_entry_list`.`entryid` INNER JOIN `db_material_inout` ON `db_material_inout`.`inoutid` = `db_godown_entry_list`.`inoutid` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_godown_entry_list`.`entryid` = '$entryid' AND `db_godown_entry`.`dotype` = 'M' ORDER BY `db_godown_entry_list`.`listid` ASC";
	$result = $db->query($sql);
	$result_amount = $db->query($sql);
	if($count = $result->num_rows){
		$toal_page = ceil($count/10);
		$page = 1;
		$total_amount = 0;
		while($row_amount = $result_amount->fetch_assoc()){
			$total_amount += $row_amount['amount'];
			$total_process_cost += $row_amount['process_cost'];
		}
		$total_amount = number_format($total_amount,2);
		$total_process_cost = number_format($total_process_cost,2);
?>
<table id="main">
  <tr>
    <td valign="top"><table id="sheet">
        <caption style=" font-size:18px; line-height:25px; margin-bottom:-15px;">
        苏州希尔林机械科技有限公司<br />
        物料入库单
        </caption>
        <tr>
          <td colspan="14" style="border:none; text-align:right;">入库单号：<?php echo $array_entry['entry_number']; ?></td>
        </tr>
        <tr>
          <th width="4%">序号</th>
          <th width="8%">模具编号</th>
          <th width="12%">物料名称</th>
          <th width="12%">规格</th>
          <th width="6%">材质</th>
          <th width="8%">收货单号</th>
          <th width="8%">入库日期</th>
          <th width="6%">单价</th>
          <th width="6%">数量</th>
          <th width="4%">单位</th>
          <th width="6%">金额</th>
          <th width="6%">加工费</th>
          <th width="8%">供应商</th>
          <th width="6%">备注</th>
        </tr>
        <?php
		$i = 1;
        while($row = $result->fetch_assoc()){
		?>
        <tr>
          <td><?php echo $i; ?></td>
          <td><?php echo $row['mould_number']; ?></td>
          <td><?php echo $row['material_name']; ?></td>
          <td><?php echo $row['specification']; ?></td>
          <td><?php echo $row['texture']; ?></td>
          <td><?php echo $row['form_number']; ?></td>
          <td><?php echo $row['dodate']; ?></td>
          <td><?php echo $row['unit_price']; ?></td>
          <td><?php echo $row['inout_quantity']; ?></td>
          <td><?php echo $row['unit_name']; ?></td>
          <td><?php echo $row['amount']; ?></td>
          <td><?php echo $row['process_cost']; ?></td>
          <td><?php echo $row['supplier_cname']; ?></td>
          <td><?php echo $row['remark']; ?></td>
        </tr>
        <?php
		if($i%10 ==0 && $i != $result->num_rows){
			$page++;
		?>
        <tr>
          <td colspan="2">合计金额</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><?php echo $total_amount; ?></td>
          <td><?php echo $total_process_cost; ?></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" style="border:none;">仓管：</td>
          <td colspan="11" style="border:none;">&nbsp;</td>
          <td style="border:none;">第<?php echo ($page-1).'/'.$toal_page; ?>页</td>
        </tr>
      </table></td>
  </tr>
</table>
<table id="main">
  <tr>
    <td valign="top"><table id="sheet">
        <caption style="font-weight:bold; font-size:16px; line-height:25px; margin-bottom:-15px;">
        苏州希尔林机械科技有限公司<br />
        入库单
        </caption>
        <tr>
          <td colspan="14" style="border:none; text-align:right;">入库单号：<?php echo $array_entry['entry_number']; ?></td>
        </tr>
        <tr>
          <th width="4%">序号</th>
          <th width="8%">模具编号</th>
          <th width="10%">物料名称</th>
          <th width="12%">规格</th>
          <th width="6%">材质</th>
          <th width="8%">收货单号</th>
          <th width="8%">入库日期</th>
          <th width="6%">单价</th>
          <th width="6%">数量</th>
          <th width="4%">单位</th>
          <th width="6%">金额</th>
          <th width="6%">加工费</th>
          <th width="8%">供应商</th>
          <th width="8%">备注</th>
        </tr>
        <?php
		}
		?>
        <?php
		$i++;
		}
		?>
        <tr>
          <td colspan="2">合计金额</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><?php echo $total_amount; ?></td>
          <td><?php echo $total_process_cost; ?></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <tr>
          <td colspan="2" style="border:none;">仓管：</td>
          <td colspan="11" style="border:none;">&nbsp;</td>
          <td style="border:none;">第<?php echo $page.'/'.$toal_page; ?>页</td>
        </tr>
      </table></td>
  </tr>
</table>
<?php
	}
}
?>
</body>
</html>