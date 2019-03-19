<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$listid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql = "SELECT `db_cutter_order_list`.`orderid`,`db_cutter_order_list`.`unit_price`,`db_cutter_order_list`.`tax_rate`,`db_cutter_order_list`.`iscash`,`db_cutter_order_list`.`plan_date`,`db_cutter_order_list`.`remark`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_cutter_purchase_list`.`quantity`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_cutter_purchase`.`purchase_number`,`db_cutter_purchase`.`purchase_date`,`db_supplier`.`supplier_name`,`db_order`.`employee_name` AS `order_employee_name`,`db_purchase`.`employee_name` AS `purchase_employee_name`,(`db_cutter_purchase_list`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_cutter_order_list` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_cutter_purchase` ON `db_cutter_purchase`.`purchaseid` = `db_cutter_purchase_list`.`purchaseid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_employee` AS `db_order` ON `db_order`.`employeeid` = `db_cutter_order`.`employeeid` INNER JOIN `db_employee` AS `db_purchase` ON `db_purchase`.`employeeid` = `db_cutter_purchase`.`employeeid` WHERE `db_cutter_order_list`.`listid` = '$listid' AND `db_cutter_order`.`employeeid` = '$employeeid' AND `db_cutter_order_list`.`listid` NOT IN (SELECT `listid` FROM `db_cutter_inout` WHERE `listid` = '$listid' GROUP BY `listid`) AND `db_cutter_order_list`.`listid` NOT IN (SELECT `linkid` FROM `db_cash_pay` WHERE `linkid` = '$listid' AND `data_type` = 'MC' GROUP BY `linkid`)";
$result = $db->query($sql);
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
<script language="javascript" type="text/javascript">
$(function(){							 
	$("#unit_price").blur(function(){
		var unit_price = $(this).val();
		if($.trim(unit_price) && !rf_b.test(unit_price)){
			alert('请输入大于零的数字')
			$(this).val('');
			$("#amount").val('');
		}else{
			if($.trim(unit_price)){
				$(this).val(parseFloat($(this).val()).toFixed(2));
				var quantity = $("#quantity").val();
				var amount = quantity*unit_price;
				$("#amount").val(amount.toFixed(2));
			}
		}
	})
})
</script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result->num_rows){
	$array = $result->fetch_assoc();
	$orderid = $array['orderid'];
?>
<div id="table_sheet">
  <h4>刀具订单明细修改</h4>
  <form action="cutter_order_list_editdo.php" name="cutter_order_list_edit" method="post">
    <table>
      <tr>
        <th width="10%">合同号：</th>
        <td width="15%"><?php echo $array['order_number']; ?></td>
        <th width="10%">订单日期：</th>
        <td width="15%"><?php echo $array['order_date']; ?></td>
        <th width="10%">供应商：</th>
        <td width="15%"><?php echo $array['supplier_name']; ?></td>
        <th width="10%">操作人：</th>
        <td width="15%"><?php echo $array['order_employee_name']; ?></td>
      </tr>
      <tr>
        <th>申购单号：</th>
        <td><?php echo $array['purchase_number']; ?></td>
        <th>申购日期：</th>
        <td><?php echo $array['purchase_date']; ?></td>
        <th>申购人：</th>
        <td colspan="3"><?php echo $array['purchase_employee_name']; ?></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><?php echo $array['type']; ?></td>
        <th>规格：</th>
        <td><?php echo $array['specification']; ?></td>
        <th>材质：</th>
        <td><?php echo $array_cutter_texture[$array['texture']]; ?></td>
        <th>硬度：</th>
        <td><?php echo $array['specification']; ?></td>
      </tr>
      <tr>
        <th>品牌：</th>
        <td><?php echo $array['brand']; ?></td>
        <th>数量：</th>
        <td><input type="text" name="quantity" id="quantity" value="<?php echo $array['quantity']; ?>" class="input_txt" readonly="readonly" />
          件</td>
        <th>单价：</th>
        <td><input type="text" name="unit_price" id="unit_price" value="<?php echo $array['unit_price']; ?>" class="input_txt" /></td>
        <th>金额：</th>
        <td><input type="text" name="amount" id="amount" value="<?php echo $array['amount']; ?>" class="input_txt" readonly="readonly" /></td>
      </tr>
      <tr>
        <th>税率：</th>
        <td><select name="tax_rate">
            <?php foreach($array_tax_rate as $tax_rate){ ?>
            <option value="<?php echo $tax_rate; ?>"<?php if($tax_rate == $array['tax_rate']) echo " selected=\"selected\""; ?>><?php echo $tax_rate*100; ?>%</option>
            <?php } ?>
          </select></td>
        <th>现金：</th>
        <td><select name="iscash">
            <?php foreach($array_is_status as $is_status_key=>$is_status_value){ ?>
            <option value="<?php echo $is_status_key; ?>"<?php if($is_status_key == $array['iscash']) echo " selected=\"selected\""; ?>><?php echo $is_status_value; ?></option>
            <?php } ?>
          </select></td>
        <th>计划回厂日期：</th>
        <td><input type="text" name="plan_date" value="<?php echo $array['plan_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>备注：</th>
        <td><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" />
          <input type="hidden" name="listid" value="<?php echo $listid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>