<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$listid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
//查询计量单位
$sql_unit = "SELECT `unitid`,`unit_name` FROM `db_unit` ORDER BY `unitid` ASC";
$result_unit = $db->query($sql_unit);
if($result_unit->num_rows){
	while($row_unit = $result_unit->fetch_assoc()){
		$array_unit[$row_unit['unitid']] = $row_unit['unit_name'];
	}
}else{
	$array_unit = array();
}
$sql = "SELECT `db_material_order_list`.`listid`,`db_material_order_list`.`materialid`,`db_material_order_list`.`order_quantity`,`db_material_order_list`.`actual_quantity`,`db_material_order_list`.`unit_price`,`db_material_order_list`.`unitid`,`db_material_order_list`.`actual_unitid`,`db_material_order_list`.`tax_rate`,ROUND(`db_material_order_list`.`actual_quantity`*`db_material_order_list`.`unit_price`,2) AS `amount`,`db_material_order_list`.`process_cost`,`db_material_order_list`.`iscash`,`db_material_order_list`.`plan_date`,`db_material_order_list`.`remark`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_material_order`.`order_number`,`db_material_order`.`order_date`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_material_order_list` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_material_order`.`employeeid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` WHERE `db_material_order_list`.`listid` = '$listid' AND `db_material_order`.`employeeid` = '$employeeid' AND `db_material_order_list`.`listid` NOT IN (SELECT `listid` FROM `db_material_inout` WHERE `listid` = '$listid' GROUP BY `listid`) AND `db_material_order_list`.`listid` NOT IN (SELECT `linkid` FROM `db_cash_pay` WHERE `linkid` = '$listid' AND `data_type` = 'M' GROUP BY `linkid`)";
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
	/*
	$("#order_quantity").blur(function(){
		var order_quantity = $(this).val();
		if(!rf_b.test(order_quantity)){
			alert('请输入大于零的数字')
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2));
			var materialid = $("#materialid").val();
			$.post('../ajax_function/material_order_quantity.php',{
			materialid:materialid
			},function(data,textstatus){
				var unit_price = $("#unit_price").val();
				var actual_quantity = order_quantity*data;
				$("#actual_quantity").val(parseFloat(actual_quantity).toFixed(2));
				var amount = actual_quantity*unit_price;
				$("#amount").val(amount.toFixed(2));
			})
		}
	})
	*/
	$("#actual_quantity").blur(function(){
		var actual_quantity = $(this).val();
		if(!rf_a.test(actual_quantity)){
			alert('请输入数字');
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2));
			var unit_price = $("#unit_price").val();
			var amount = actual_quantity*unit_price;
			$("#amount").val(amount.toFixed(2));
		}
	})
	$("#actual_quantity").focus(function(){
		var materialid = $("#materialid").val();
		$.post('../ajax_function/material_order_quantity.php',{
			materialid:materialid
		},function(data,textstatus){
			var array_data = data.split('#');
			var actual_quantity = array_data[0];	
			var unitid = array_data[1];
			$("#actual_quantity").val(actual_quantity);
			$("#actual_unitid").find("option[value="+unitid+"]").attr("selected",true);
			var unit_price = $("#unit_price").val();
			var amount = actual_quantity*unit_price;
			$("#amount").val(amount.toFixed(2));
		})
	})
	$("#unit_price").blur(function(){
		var unit_price = $(this).val();
		if(!rf_b.test(unit_price)){
			alert('请输入大于零的数字')
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2));
			var actual_quantity = $("#actual_quantity").val();
			var amount = actual_quantity*unit_price;
			$("#amount").val(amount.toFixed(2));
		}
	})
	/*
	$("#amount").blur(function(){
		var unit_price = $(this).val();
		if(!rf_a.test(unit_price)){
			alert('请输入数字')
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2));
		}
	})
	*/
	$("input[name=process_cost]").blur(function(){
		var process_cost = $(this).val();
		if($.trim(process_cost) && !rf_a.test(process_cost)){
			alert('请输入数字')
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
})
</script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
  ?>
  <h4>物料订单明细修改</h4>
  <form action="material_order_list_editdo.php" name="material_order_list_edit" method="post">
    <table>
      <tr>
        <th width="10%">合同编号：</th>
        <td width="15%"><?php echo $array['order_number']; ?></td>
        <th width="10%">订单日期：</th>
        <td width="15%"><?php echo $array['order_date']; ?></td>
        <th width="10%">供应商：</th>
        <td width="15%"><?php echo $array['supplier_cname']; ?></td>
        <th width="10%">操作人：</th>
        <td width="15%"><?php echo $array['employee_name']; ?></td>
      </tr>
      <tr>
        <th>模具名称：</th>
        <td><?php echo $array['mould_number']; ?></td>
        <th>物料名称：</th>
        <td><?php echo $array['material_name']; ?></td>
        <th>规格：</th>
        <td><?php echo $array['specification']; ?></td>
        <th>材质：</th>
        <td><?php echo $array['texture']; ?></td>
      </tr>
      <tr>
        <th>订单数量：</th>
        <td><input type="text" name="order_quantity" id="order_quantity" value="<?php echo $array['order_quantity']; ?>" class="input_txt" readonly="readonly" /></td>
        <th>单位：</th>
        <td><select name="unitid">
            <?php
			foreach($array_unit as $unitid=>$unit_name){
			?>
            <option value="<?php echo $unitid; ?>"<?php if($unitid == $array['unitid']) echo " selected=\"selected\""; ?>><?php echo $unit_name; ?></option>
            <?php
			}
			?>
          </select></td>
        <th>实际数量：</th>
        <td><input type="text" name="actual_quantity" id="actual_quantity" value="<?php echo $array['actual_quantity']; ?>" class="input_txt" /></td>
        <th>单位：</th>
        <td><select name="actual_unitid" id="actual_unitid">
            <?php
			foreach($array_unit as $unitid=>$unit_name){
			?>
            <option value="<?php echo $unitid; ?>"<?php if($unitid == $array['actual_unitid']) echo " selected=\"selected\""; ?>><?php echo $unit_name; ?></option>
            <?php
			}
			?>
          </select></td>
      </tr>
      <tr>
        <th>单价(含税)：</th>
        <td><input type="text" name="unit_price" id="unit_price" value="<?php echo $array['unit_price']; ?>" class="input_txt" /></td>
        <th>税率：</th>
        <td><select name="tax_rate" id="tax_rate">
            <?php foreach($array_tax_rate as $tax_rate){ ?>
            <option value="<?php echo $tax_rate; ?>"<?php if($tax_rate == $array['tax_rate']) echo " selected=\"selected\""; ?>><?php echo ($tax_rate*100); ?>%</option>
            <?php } ?>
          </select></td>
        <th>金额(含税)：</th>
        <td><input type="text" name="amount" id="amount" value="<?php echo $array['amount']; ?>" class="input_txt" readonly="readonly" /></td>
        <th>加工费：</th>
        <td><input type="text" name="process_cost" id="process_cost" value="<?php echo $array['process_cost']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>现金：</th>
        <td><select name="iscash">
            <?php foreach($array_is_status as $is_status_key=>$is_status_value){ ?>
            <option value="<?php echo $is_status_key; ?>"<?php if($is_status_key == $array['iscash']) echo " selected=\"selected\""; ?>><?php echo $is_status_value; ?></option>
            <?php } ?>
          </select></td>
        <th>计划回厂日期：</th>
        <td><input type="text" name="plan_date" value="<?php echo $array['plan_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>备注：</th>
        <td colspan="5"><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="5"><input type="submit" name="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="materialid" id="materialid" value="<?php echo $array['materialid']; ?>" />
          <input type="hidden" name="listid" value="<?php echo $listid; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>