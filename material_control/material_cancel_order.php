<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$diff = $_GET['diff'];
$diff = floatval($diff);
$inoutid = $_GET['inoutid'];
//通过入库记录的id查询订单id并更改订单信息
$sql = "UPDATE `db_material_inout` SET `cancel_num` = $diff WHERE `inoutid` = $inoutid";

$db->query($sql);

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
if($db->affected_rows){
    $sql_order = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`listid`,`db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`amount`,`db_material_inout`.`process_cost`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`plan_date`,IF(`db_material_order_list`.`actual_unitid`='3',0.00,`db_material_order_list`.`order_quantity`-`db_material_inout`.`inout_quantity`) AS `diff_date`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit_order`.`unit_name` AS `unit_name_order`,`db_unit_actual`.`unit_name` AS `unit_name_actual` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_inout`.`inoutid`=$inoutid";
	$result = $db->query($sql_order);
	$result_amount = $db->query($sql_order);
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
        苏州嘉泰隆实业有限公司<br />
        模具物料核销单
        </caption>
        <tr>
          <td colspan="14" style="border:none; text-align:right;"><?php echo $array_entry['entry_number']; ?></td>
        </tr>
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
      <th width="8%">供应商</th>
      <th width="6%">入库日期</th>
      <th width="8%">货运单号</th>
      <!-- <th width="6%">计划回厂日期</th> -->
      <th width="4%">核销<br />
      数量</th>
    </tr>
        <?php
		$i = 1;
        while($row = $result->fetch_assoc()){
		?>
        <tr>
            <td><?php echo $row['inoutid']; ?></td>
            <td><?php echo $row['order_number']; ?></td>
            <td><?php echo $row['mould_number']; ?></td>
            <td><?php echo $row['material_name']; ?></td>
            <td><?php echo $row['specification']; ?></td>
            <td><?php echo $row['texture']; ?></td>
            <td><?php echo $row['quantity'].$rows['unit_name_order']; ?></td>
            <td><?php echo $row['inout_quantity'].$rows['unit_name_actual']; ?></td>
            <td><?php echo $row['unit_price']; ?></td>
            <td><?php echo $row['amount']; ?></td>
            <td><?php echo $row['process_cost']; ?></td>
            <td><?php echo $row['supplier_cname']; ?></td>
            <td><?php echo $row['dodate']; ?></td>
            <td><?php echo $row['form_number']; ?></td>
            <!-- <td><?php echo $row['plan_date']; ?></td> -->
            <td><?php echo $row['diff_date']; ?></td>       
       </tr>
        <?php
		if($i%10 ==0 && $i != $result->num_rows){
			$page++;
		?>
      <!--   <tr>
          <td colspan="2"></td>
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
        </tr> -->
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
      <th width="8%">供应商</th>
      <th width="6%">入库日期</th>
      <th width="8%">货运单号</th>
<!--       <th width="6%">计划回厂日期</th> -->
      <th width="4%">核销数量</th>

        </tr>
        <?php
		}
		?>
        <?php
		$i++;
		}
		?>
       <!--  <tr>
          <td colspan="2"></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr> -->
        <tr>
          <td colspan="2" style="border:none;">签字：</td>
          <td colspan="8" style="border:none;">&nbsp;</td>
          <td colspan="2" style="border:none;">日期:<?php echo date('Y-m-d') ?></td>
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