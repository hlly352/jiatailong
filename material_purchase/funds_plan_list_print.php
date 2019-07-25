<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$planid = fun_check_int($_GET['id']);
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

  $sql = "SELECT `db_material_funds_plan`.`plan_number`,`db_funds_plan_list`.`plan_amount`,`db_material_account`.`apply_amount`,`db_material_invoice_list`.`date`,`db_material_account`.`accountid`,`db_material_account`.`account_time`,`db_material_account`.`amount`,`db_supplier`.`supplier_cname` FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_account_list` ON `db_material_account`.`accountid` = `db_material_account_list`.`accountid` INNER JOIN `db_material_inout` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` INNER JOIN `db_material_invoice_list` ON `db_material_invoice_list`.`accountid` = `db_material_account`.`accountid` INNER JOIN `db_funds_plan_list` ON `db_material_account`.`accountid` = `db_funds_plan_list`.`accountid` INNER JOIN `db_material_funds_plan` ON `db_material_funds_plan`.`planid` = `db_funds_plan_list`.`planid` WHERE `db_material_inout`.`account_status` = 'M' AND `db_funds_plan_list`.`planid` = '$planid' GROUP BY `db_material_account`.`accountid`";
	$result = $db->query($sql);
	$result_amount = $db->query($sql);
	if($count = $result->num_rows){
		$toal_page = ceil($count/10);
		$page = 1;
		$total_amount = 0;
		while($row_amount = $result_amount->fetch_assoc()){
      $plan_number = $row_amount['plan_number'];
			$total_amount += $row_amount['amount'];
		}
		$total_amount = number_format($total_amount,2);
?>
<table id="main">
  <tr>
    <td valign="top"><table id="sheet">
        <caption style=" font-size:18px; line-height:25px; margin-bottom:-15px;">
        苏州希尔林实业有限公司<br />
        付款申请单
        </caption>
        <tr>
          <td colspan="7" style="border:none; text-align:right;">申请单号：<?php echo $plan_number; ?></td>
        </tr>
        <tr>
          <th>ID</th>
        <th>对账时间</th>
        <th>发票时间</th>
        <th>供应商名称</th>
        <th>对账金额</th>
        <th width="17%">计划金额</th>
        <th width="20%">发票号</th>
        </tr>

        <?php
		$i = 1;
        while($row = $result->fetch_assoc()){
                  //查询对应的发票号
        $invoice_sql = "SELECT `invoice_no` FROM `db_material_invoice_list` WHERE `accountid`=".$row['accountid'];
        $result_invoice = $db->query($invoice_sql);
		?>
        <tr>
          <td><?php echo $i; ?></td>
          <td><?php echo $row['account_time'] ?></td>
        <td><?php echo $row['date'] ?></td>
        <td><?php echo $row['supplier_cname'] ?></td>
        <td class="amount" id="amount-<?php echo $row['accountid'] ?>"><?php echo number_format($row['amount'],2,'.','') ?></td>
        <td>
          <?php echo number_format($row['plan_amount'],2,'.','') ?>
        </td>
        <td>
          <?php
            if($result_invoice->num_rows){
              while($row_invoice = $result_invoice->fetch_assoc()){
                echo ' PO:'.$row_invoice['invoice_no'];
              }
            }
          ?>
        </td>
        </tr>
        <?php } ?>
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
		
		?>
        <tr>
          <td colspan="2">合计金额</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><?php echo $total_amount; ?></td>
          <td></td>
    
        </tr>
        <tr>
          <td colspan="2" style="border:none;">签字：</td>
          <td colspan="3" style="border:none;">&nbsp;</td>
          <td style="border:none;">第<?php echo $page.'/'.$toal_page; ?>页</td>
        </tr>
      </table></td>
  </tr>
</table>
<?php
	}

?>
</body>
</html>