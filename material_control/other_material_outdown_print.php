<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$entryid = fun_check_int($_GET['id']);
$sql_entry = "SELECT `entry_number` FROM `db_outdown` WHERE `entryid` = '$entryid' AND `dotype` = 'O'";
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
<title>物料出库单打印-希尔林</title>
</head>

<body>
<?php
if($result_entry->num_rows){
	$array_entry = $result_entry->fetch_assoc();
	// $sql = "SELECT `db_other_material_data`.`material_name`,`db_mould_other_material`.`material_specification`,`db_other_material_inout`.`dodate`,`db_other_material_inout`.`actual_quantity`,`db_mould_other_material`.`unit`,`db_supplier`.`supplier_cname`,`db_other_material_inout`.`form_number`,`db_other_material_inout`.`remark` FROM `db_outdown_list` INNER JOIN `db_outdown` ON `db_outdown`.`entryid` = `db_outdown_list`.`entryid` INNER JOIN `db_other_material_inout` ON `db_other_material_inout`.`inoutid` = `db_outdown_list`.`inoutid` INNER JOIN `db_other_material_orderlist` ON `db_other_material_orderlist`.`listid` = `db_other_material_inout`.`listid` INNER JOIN `db_other_material_order` ON `db_other_material_order`.`orderid` = `db_other_material_orderlist`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_other_material_order`.`supplierid` INNER JOIN `db_mould_other_material` ON `db_mould_other_material`.`mould_other_id` = `db_other_material_orderlist`.`materialid` INNER JOIN `db_other_material_data` ON `db_other_material_data`.`dataid` = `db_mould_other_material`.`material_name` WHERE `db_outdown_list`.`entryid` = '$entryid' AND `db_outdown`.`dotype` = 'O' ORDER BY `db_outdown_list`.`listid` ASC";
$sql = "SELECT `db_outdown_list`.`listid`,`db_other_material_inout`.`inout_quantity`,`db_other_material_specification`.`specificationid`,`db_other_material_specification`.`material_name`,`db_other_material_inout`.`inoutid` ,`db_other_material_specification`.`type`,`db_other_material_specification`.`materialid`,`db_other_material_inout`.`taker`,`db_other_material_inout`.`dodate`,`db_other_material_inout`.`remark`,`db_other_material_inout`.`form_number`,`db_other_material_specification`.`material_name`,`db_other_material_specification`.`specification_name` FROM `db_outdown_list` INNER JOIN `db_outdown` ON `db_outdown_list`.`entryid` = `db_outdown`.`entryid` INNER JOIN `db_other_material_inout` ON `db_outdown_list`.`inoutid` = `db_other_material_inout`.`inoutid` INNER JOIN `db_other_material_specification` ON `db_other_material_inout`.`listid` = `db_other_material_specification`.`specificationid` WHERE `db_outdown_list`.`entryid` = '$entryid' AND `db_outdown`.`dotype` = 'O'";

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
        <caption style=" font-size:18px; line-height:25px; margin-bottom:-15px;">
        苏州希尔林实业有限公司<br />
        物料出库单
        </caption>
        <tr>
          <td colspan="9" style="border:none; text-align:right;">出库单号：<?php echo $array_entry['entry_number']; ?></td>
        </tr>
        <tr>
          <th width="4%">序号</th>
          <th width="12%">物料名称</th>
          <th width="12%">规格</th>
          <th width="8%">出库日期</th>
          <th width="6%">数量</th>
          <th width="4%">单位</th>
          <th width="8%">供应商</th>
          <th width="8%">收货单号</th>
          <th width="6%">备注</th>
        </tr>
        <?php
		$i = 1;
        while($row = $result->fetch_assoc()){
           $listid = $row['listid'];
          if($row['type'] == 'A'){
              $sql_info = "SELECT `material_name`,`unit` FROM `db_other_material_data` WHERE `dataid` = ".$row['materialid'];
            }elseif($row['type'] == 'B'){
              $sql_info = "SELECT `unit` FROM `db_mould_other_material` WHERE `mould_other_id` = ".$row['materialid'];
            }
            $result_info = $db->query($sql_info);
            if($result_info->num_rows){
              $info = $result_info->fetch_assoc();
            }
		?>
        <tr>
          <td><?php echo $i; ?></td>
           <td><?php echo $row['material_name']?$row['material_name']:$info['material_name']; ?></td>
          <td><?php echo $row['specification_name']; ?></td>
          <td><?php echo $row['inout_quantity']; ?></td>
          <td><?php echo $info['unit']; ?></td>
          <td><?php echo $row['taker']; ?></td>
          <td><?php echo $row['form_number']; ?></td>
          <td><?php echo $row['dodate']; ?></td>
          <td><?php echo $row['remark']; ?></td>
        </tr>
        <?php
		if($i%10 ==0 && $i != $result->num_rows){
			$page++;
		?>
        <!-- <tr>
          <td colspan="2">合计金额</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><?php echo $total_amount; ?></td>
          <td></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr> -->
        <tr>
          <td colspan="2" style="border:none;">仓管：</td>
          <td colspan="5" style="border:none;">&nbsp;</td>
          <td style="border:none;">签字：</td>
        </tr>
        <tr>
          <td colspan="9" style="border:none;">第<?php echo $page.'/'.$toal_page; ?>页</td>
        </tr>
      </table></td>
  </tr>
</table>
<table id="main">
  <tr>
    <td valign="top"><table id="sheet">
        <caption style="font-weight:bold; font-size:16px; line-height:25px; margin-bottom:-15px;">
        苏州希尔林实业有限公司<br />
        出库单
        </caption>
        <tr>
          <td colspan="14" style="border:none; text-align:right;">出库单号：<?php echo $array_entry['entry_number']; ?></td>
        </tr>
        <tr>
          <th width="4%">序号</th>
          <th width="12%">物料名称</th>
          <th width="12%">规格</th>
          <th width="8%">出库日期</th>
          <th width="6%">单价</th>
          <th width="6%">数量</th>
          <th width="4%">单位</th>
          <th width="6%">金额</th>
          <th width="8%">供应商</th>
          <th width="8%">收货单号</th>
          <th width="6%">备注</th>
        </tr>
        <?php
		}
		?>
        <?php
		$i++;
		}
		?>
       <!--  <tr>
          <td colspan="2">合计金额</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><?php echo $total_amount; ?></td>
          <td></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr> -->
        <tr>
          <td colspan="2" style="border:none;">仓管：</td>
          <td colspan="5" style="border:none;">&nbsp;</td>
          <td style="border:none;">签字：</td>
        </tr>
        <tr>
          <td colspan="8" style="border:none"></td>
          <td  style="border:none;">第<?php echo $page.'/'.$toal_page; ?>页</td>
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