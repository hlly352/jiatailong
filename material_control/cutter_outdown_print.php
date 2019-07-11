<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$entryid = fun_check_int($_GET['id']);
$sql_entry = "SELECT `entry_number` FROM `db_outdown` WHERE `entryid` = '$entryid' AND `dotype` = 'C'";
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
<title>刀具出库单打印-希尔林</title>
</head>

<body>
<?php
if($result_entry->num_rows){
	$array_entry = $result_entry->fetch_assoc();
	$sql = "SELECT `db_cutter_inout`.`quantity`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`remark`,`db_cutter_inout`.`form_number`,`db_cutter_order_list`.`unit_price`,`db_cutter_order`.`order_number`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,(`db_cutter_inout`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_outdown_list` INNER JOIN `db_outdown` ON `db_outdown`.`entryid` = `db_outdown_list`.`entryid` INNER JOIN `db_cutter_inout` ON `db_cutter_inout`.`inoutid` = `db_outdown_list`.`inoutid` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` WHERE `db_outdown_list`.`entryid` = '$entryid' AND `db_outdown`.`dotype` = 'C' ORDER BY `db_outdown_list`.`listid` ASC";
	$result = $db->query($sql);
	$result_amount = $db->query($sql);
	if($count = $result->num_rows){
		$toal_page = ceil($count/10);
		$page = 1;
		$total_amount = 0;
		while($row_amount = $result_amount->fetch_assoc()){
			$total_amount += $row_amount['amount'];
		}
		$total_amount = number_format($total_amount,2);
?>
<table id="main">
  <tr>
    <td valign="top"><table id="sheet">
        <caption style="font-size:18px; line-height:25px; margin-bottom:-15px;">
        苏州希尔林机械科技有限公司<br />
        刀具出库单
        </caption>
        <tr>
          <td colspan="14" style="border:none; text-align:right;">出库单号：<?php echo $array_entry['entry_number']; ?></td>
        </tr>
        <tr>
          <th width="4%">序号</th>
          <th width="6%">类型</th>
          <th width="12%">规格</th>
          <th width="6%">材质</th>
          <th width="10%">硬度</th>
          <th width="8%">品牌</th>
          <th width="10%">收货单号</th>
          <th width="8%">出库日期</th>
          <th width="6%">单价</th>
          <th width="6%">数量</th>
          <th width="4%">单位</th>
          <th width="6%">金额</th>
          <th width="8%">供应商</th>
          <th width="6%">备注</th>
        </tr>
        <?php
		$i = 1;
        while($row = $result->fetch_assoc()){
		?>
        <tr>
          <td><?php echo $i; ?></td>
          <td><?php echo $row['type']; ?></td>
          <td><?php echo $row['specification']; ?></td>
          <td><?php echo $array_cutter_texture[$row['texture']]; ?></td>
          <td><?php echo $row['hardness']; ?></td>
          <td><?php echo $row['brand']; ?></td>
          <td><?php echo $row['form_number']; ?></td>
          <td><?php echo $row['dodate']; ?></td>
          <td><?php echo $row['unit_price']; ?></td>
          <td><?php echo $row['quantity']; ?></td>
          <td>件</td>
          <td><?php echo $row['amount']; ?></td>
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
          <td>&nbsp;</td>
          <td><?php echo $total_amount; ?></td>
          <td>111</td>
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
        出库单
        </caption>
        <tr>
          <td colspan="14" style="border:none; text-align:right;">出库单号：<?php echo $array_entry['entry_number']; ?></td>
        </tr>
        <tr>
          <th width="4%">序号</th>
          <th width="6%">类型</th>
          <th width="12%">规格</th>
          <th width="6%">材质</th>
          <th width="10%">硬度</th>
          <th width="8%">品牌</th>
          <th width="10%">收货单号</th>
          <th width="8%">出库日期</th>
          <th width="6%">单价</th>
          <th width="6%">数量</th>
          <th width="4%">单位</th>
          <th width="6%">金额</th>
          <th width="8%">供应商</th>
          <th width="6%">备注</th>
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
          <td>&nbsp;</td>
          <td><?php echo $total_amount; ?></td>
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