<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
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
	$("#submit").click(function(){
		var action = $("#action").val();
		var form_number = $("#form_number").val();
		if(!$.trim(form_number)){
			$("#form_number").focus();
			return false;
		}
		if(action == "add"){
			var quantity = $("#quantity").val();
			if(!rf_b.test(quantity)){
				$("#quantity").focus();
				return false;
			}
			var inout_quantity = $("#inout_quantity").val();
			if(!rf_b.test(inout_quantity)){
				$("#inout_quantity").focus();
				return false;
			}
		}
		var amount = $("#amount").val();
		if(!rf_b.test(amount)){
			$("#amount").focus();
			return false;
		}
	})
	$("#inout_quantity").blur(function(){	
		var inout_quantity = $(this).val();
		if(!rf_b.test(inout_quantity)){
			alert('请输入大于零的数字');
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2));
			var unit_price = $("#unit_price").val();
			var amount = (inout_quantity*unit_price).toFixed(2);
			$("#amount").val(amount);
		}
	})
	$("#process_cost").blur(function(){	
		var process_cost = $(this).val();
		if($.trim(process_cost) && !rf_a.test(process_cost)){
			alert('请输入数字');
			$(this).val(this.defaultValue);
		}else{
			if($.trim(process_cost)){
				$(this).val(parseFloat($(this).val()).toFixed(2));
			}else{
				$(this).val(this.defaultValue);
			}
		}
	}).focus(function(){
		var process_cost = $(this).val();
		if(process_cost == 0){
			$(this).val('');
		}
	})
	$("#quantity").blur(function(){
		var default_quantity = this.defaultValue;
		var quantity = $(this).val();
		var listid = $("#listid").val();
		if(!rf_b.test(quantity)){
			alert('请输入大于零的数字');
			$(this).val(this.defaultValue);
		}else{
			$.post('../ajax_function/material_in_quantity_check.php',{
				quantity:quantity,
				listid:listid
			},function(data,textstatus){
				if(data == 0){
					alert('入库数量异常！');
					$("#quantity").val(default_quantity);
				}
			})
		}
	})
})
</script>
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == "add"){
	  $listid = fun_check_int($_GET['id']);
	 // $sql = "SELECT (`db_material_order_list`.`order_quantity`-`db_material_order_list`.`in_quantity`) AS `quantity`,(`db_material_order_list`.`actual_quantity`-IF((SELECT SUM(`inout_quantity`) FROM `db_material_inout` WHERE `dotype` = 'I' AND `listid` = '$listid'),(SELECT SUM(`inout_quantity`) FROM `db_material_inout` WHERE `dotype` = 'I' AND `listid` = '$listid'),0)) AS `inout_quantity`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`unitid`,`db_material_order_list`.`tax_rate`,ROUND(((`db_material_order_list`.`actual_quantity`-IF((SELECT SUM(`inout_quantity`) FROM `db_material_inout` WHERE `dotype` = 'I' AND `listid` = '$listid'),(SELECT SUM(`inout_quantity`) FROM `db_material_inout` WHERE `dotype` = 'I' AND `listid` = '$listid'),0))*`db_material_order_list`.`unit_price`),2) AS `amount`,`db_material_order_list`.`process_cost`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name`,`db_unit_actual`.`unit_name` AS `actual_unit_name` FROM `db_material_order_list` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_order`.`order_status` = 1 AND (`db_material_order_list`.`order_quantity`-`db_material_order_list`.`in_quantity`) > 0 AND `db_material_order_list`.`listid` = '$listid'";
      $sql = "SELECT * FROM `db_other_material_orderlist` INNER JOIN `db_other_material_order` ON `db_other_material_orderlist`.`orderid` = `db_other_material_order`.`orderid` INNER JOIN `db_mould_other_material` ON `db_mould_other_material`.`mould_other_id` = `db_other_material_orderlist`.`materialid` INNER JOIN `db_other_supplier` ON `db_other_material_order`.`supplierid` = `db_other_supplier`.`other_supplier_id` WHERE `db_other_material_orderlist`.`listid` = $listid"; 
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
      //获取金额
      $amount = floatval($array['unit_price']) * floatval($array['actual_quantity']);
      $amount = number_format($amount,2,'.','');
  ?>
  <h4>订单物料入库</h4>
  <form action="other_material_inlist_indo.php" name="material_order_list_in" method="post">
    <table>
      <tr>
        <th width="10%">合同号：</th>
        <td width="15%"><?php echo $array['order_number']; ?></td>
        <th width="10%">模具编号：</th>
        <td width="15%"><?php echo $array['mould_no']; ?></td>
        <th width="10%">物料名称：</th>
        <td width="15%"><?php echo $array['material_name']; ?></td>
        <th width="10%">规格：</th>
        <td width="15%"><?php echo $array['material_specification']; ?></td>
      </tr>
      <tr>
        <th>供应商：</th>
        <td><?php echo $array['supplier_cname']; ?></td>
        <th>实际回厂日期：</th>
        <td><input type="text" name="dodate" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>送货单号：</th>
        <td><input type="text" name="form_number" id="form_number" class="input_txt" />
          <span class="tag">*</span></td>
        <th>税率：</th>
        <td><?php echo $array['tax_rate']*100; ?>%</td>
      </tr>
      <tr>
        <th>订单数量：</th>
        <td><input type="text" name="actual_quantity" id="quantity" value="<?php echo $array['actual_quantity']; ?>" readonly class="input_txt" />
          <?php echo $array['unit_name']; ?></td>
        <th>实际数量：</th>
        <td><input type="text" name="inout_quantity" id="inout_quantity" value="<?php echo $array['actual_quantity']; ?>" class="input_txt" />
          <?php echo $array['unit']; ?></td>
        <th>单价：</th>
        <td><input type="text" name="unit_price" id="unit_price" value="<?php echo $array['unit_price']; ?>" class="input_txt" readonly="readonly" /></td>
         <th>金额(含税)：</th>
        <td><input type="text" name="amount" id="amount" value="<?php echo $amount; ?>" class="input_txt" /></td>
      </tr>
      <tr>
       
        <th>备注：</th>
        <td colspan="3"><input type="text" name="remark" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="mould_other_id" value="<?php echo $array['mould_other_id'] ?>" />
          <input type="hidden" name="listid" id="listid" value="<?php echo $listid; ?>" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		   echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }elseif($action == "edit"){
	  $inoutid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`unitid`,`db_material_inout`.`remark`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`tax_rate`,`db_material_inout`.`process_cost`,`db_material_inout`.`amount`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name`,`db_unit_actual`.`unit_name` AS `actual_unit_name` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` WHERE `db_material_inout`.`inoutid` = '$inoutid' AND `db_material_inout`.`dotype` = 'I'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>订单物料入库修改</h4>
  <form action="material_in_list_indo.php" name="material_order_list_in" method="post">
    <table>
      <tr>
        <th width="10%">合同号：</th>
        <td width="15%"><?php echo $array['order_number']; ?></td>
        <th width="10%">模具编号：</th>
        <td width="15%"><?php echo $array['mould_number']; ?></td>
        <th width="10%">物料名称：</th>
        <td width="15%"><?php echo $array['material_name']; ?></td>
        <th width="10%">规格：</th>
        <td width="15%"><?php echo $array['specification']; ?></td>
      </tr>
      <tr>
        <th>材质：</th>
        <td><?php echo $array['texture']; ?></td>
        <th>供应商：</th>
        <td><?php echo $array['supplier_cname']; ?></td>
        <th>实际回厂日期：</th>
        <td><input type="text" name="dodate" value="<?php echo $array['dodate']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>送货单号：</th>
        <td><input type="text" name="form_number" id="form_number" value="<?php echo $array['form_number']; ?>" class="input_txt" />
          <span class="tag">*</span></td>
      </tr>
      <tr>
        <th>订单数量：</th>
        <td><?php echo $array['quantity'].$array['unit_name']; ?></td>
        <th>实际数量：</th>
        <td><input type="text" name="inout_quantity" id="inout_quantity" value="<?php echo $array['inout_quantity']; ?>" class="input_txt" />
          <?php echo $array['actual_unit_name']; ?></td>
        <th>单价：</th>
        <td><input type="text" name="unit_price" id="unit_price" value="<?php echo $array['unit_price']; ?>" class="input_txt" readonly="readonly" /></td>
        <th>税率：</th>
        <td><?php echo $array['tax_rate']*100; ?>%</td>
      </tr>
      <tr>
        <th>金额(含税)：</th>
        <td><input type="text" name="amount" id="amount" value="<?php echo $array['amount']; ?>" class="input_txt" /></td>
        <th>加工费：</th>
        <td><input type="text" name="process_cost" id="process_cost" value="<?php echo $array['process_cost']; ?>" class="input_txt" /></td>
        <th>备注：</th>
        <td colspan="3"><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="inoutid" id="inoutid" value="<?php echo $inoutid; ?>" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }else{
		  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
	  }
  }
  ?>
</div>
<?php
$sql_inout = "SELECT `db_material_inout`.`inoutid`,`db_material_inout`.`form_number`,`db_material_inout`.`dodate`,`db_material_inout`.`quantity`,`db_material_inout`.`inout_quantity`,`db_material_inout`.`remark`,`db_material_inout`.`dotime`,`db_material_order_list`.`unit_price`,`db_material_inout`.`amount`,`db_unit_order`.`unit_name` AS `unit_name_order`,`db_unit_actual`.`unit_name` AS `unit_name_actual`,`db_employee`.`employee_name` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_unit` AS `db_unit_order` ON `db_unit_order`.`unitid` = `db_material_order_list`.`unitid` INNER JOIN `db_unit` AS `db_unit_actual` ON `db_unit_actual`.`unitid`= `db_material_order_list`.`actual_unitid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_inout`.`employeeid` WHERE `db_material_inout`.`listid` ='$listid' AND `db_material_inout`.`dotype` = 'I' ORDER BY `db_material_inout`.`inoutid` DESC";
$result_inout = $db->query($sql_inout);
?>
<div id="table_list">
  <?php if($result_inout->num_rows){ ?>
  <table>
    <caption>
    物料入库记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="12%">表单号</th>
      <th width="8%">数量</th>
      <th width="8%">实际数量</th>
      <th width="8%">单价(含税)</th>
      <th width="8%">金额(含税)</th>
      <th width="10%">出入库日期</th>
      <th width="10%">操作人</th>
      <th width="14%">操作时间</th>
      <th width="14%">备注</th>
      <th width="4%">Edit</th>
    </tr>
    <?php
    while($row_inout = $result_inout->fetch_assoc()){
		$inoutid = $row_inout['inoutid'];
	?>
    <tr>
      <td><?php echo $inoutid; ?></td>
      <td><?php echo $row_inout['form_number']; ?></td>
      <td><?php echo $row_inout['quantity'].$row_inout['unit_name_order']; ?></td>
      <td><?php echo $row_inout['inout_quantity'].$row_inout['unit_name_actual']; ?></td>
      <td><?php echo $row_inout['unit_price']; ?></td>
      <td><?php echo $row_inout['amount']; ?></td>
      <td><?php echo $row_inout['dodate']; ?></td>
      <td><?php echo $row_inout['employee_name']; ?></td>
      <td><?php echo $row_inout['dotime']; ?></td>
      <td><?php echo $row_inout['remark']; ?></td>
      <td><a href="material_in_list_in.php?id=<?php echo $inoutid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a></td>
    </tr>
    <?php
      $total += $row_inout['quantity'];
	  }
	  ?>
    <tr>
      <td>&nbsp;</td>
      <td>Total</td>
      <td><?php echo number_format($total,2).$array['unit_name']; ?></td>
      <td colspan="8">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无入库记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>