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
		if(action == "add"){
			var quantity = $("#quantity").val();
			if(!ri_b.test(quantity)){
				$("#quantity").focus();
				return false;
			}
		}
		var form_number = $("#form_number").val();
		if(!$.trim(form_number)){
			$("#form_number").focus();
			return false;
		}
	})
	$("#quantity").blur(function(){
		var default_quantity = this.defaultValue;
		var quantity = $(this).val();
		var unit_price = $("#unit_price").val();
		var listid = $("#listid").val();
		if($.trim(quantity) && !ri_b.test(quantity)){
			alert('请输入大于零的数字')
			$(this).val(this.defaultValue);
			var amount = this.defaultValue*unit_price;
			$("#amount").val(amount.toFixed(2));
		}else{
			$.post('../ajax_function/cutter_in_quantity_check.php',{
				quantity:quantity,
				listid:listid
			},function(data,textstatus){
				if(data == 0){
					alert('入库数量异常！');
					$("#quantity").val(default_quantity);
				}else if(data == 1){
					var amount = quantity*unit_price;
					$("#amount").val(amount.toFixed(2));
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
	  $sql = "SELECT `db_cutter_order_list`.`unit_price`,`db_cutter_order_list`.`tax_rate`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,(`db_cutter_purchase_list`.`quantity`-`db_cutter_order_list`.`in_quantity`) AS `quantity`,((`db_cutter_purchase_list`.`quantity`-`db_cutter_order_list`.`in_quantity`)*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_cutter_order_list` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_order`.`employeeid` WHERE `db_cutter_order_list`.`listid` = '$listid' AND (`db_cutter_purchase_list`.`quantity`-`db_cutter_order_list`.`in_quantity`) > 0";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>订单刀具入库</h4>
  <form action="cutter_in_list_indo.php" name="cutter_in_list_in" method="post">
    <table>
      <tr>
        <th width="10%">合同号：</th>
        <td width="15%"><?php echo $array['order_number']; ?></td>
        <th width="10%">订单日期：</th>
        <td width="15%"><?php echo $array['order_date']; ?></td>
        <th width="10%">供应商：</th>
        <td width="15%"><?php echo $array['supplier_cname']; ?></td>
        <th width="10%">操作人：</th>
        <td width="15%"><?php echo $array['employee_name']; ?></td>
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
        <td><input type="text" name="quantity" id="quantity" value="<?php echo $array['quantity']; ?>" class="input_txt" />
          件</td>
        <th>单价：</th>
        <td><input type="text" name="unit_price" id="unit_price" value="<?php echo $array['unit_price']; ?>" class="input_txt" readonly="readonly" /></td>
        <th>金额：</th>
        <td><input type="text" name="amount" id="amount" value="<?php echo $array['amount']; ?>" class="input_txt" readonly="readonly" /></td>
      </tr>
      <tr>
        <th>税率：</th>
        <td><?php echo $array['tax_rate']*100 ?>%</td>
        <th>入库日期：</th>
        <td><input type="text" name="dodate" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>送货单号：</th>
        <td><input type="text" name="form_number" id="form_number" class="input_txt" />
          <span class="tag">*</span></td>
        <th>备注：</th>
        <td><input type="text" name="remark" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="listid" id="listid" value="<?php echo $listid; ?>" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }
  }elseif($action == "edit"){
	  $inoutid = fun_check_int($_GET['id']);
	  $employeeid = $_SESSION['employee_info']['employeeid'];
	  $sql = "SELECT `db_cutter_inout`.`quantity`,`db_cutter_inout`.`form_number`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`remark`,`db_cutter_order_list`.`unit_price`,`db_cutter_order_list`.`tax_rate`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_cutter_brand`.`brand`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name`,(`db_cutter_inout`.`quantity`*`db_cutter_order_list`.`unit_price`) AS `amount` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_brand` ON `db_cutter_brand`.`brandid` = `db_cutter_purchase_list`.`brandid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_cutter_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_order`.`employeeid` WHERE `db_cutter_inout`.`inoutid` = '$inoutid' AND `db_cutter_inout`.`employeeid` = '$employeeid'";
	$result = $db->query($sql);
	if($result->num_rows){
		$array = $result->fetch_assoc();
  ?>
  <h4>订单刀具入库修改</h4>
  <form action="cutter_in_list_indo.php" name="cutter_in_list_in" method="post">
    <table>
      <tr>
        <th width="10%">合同号：</th>
        <td width="15%"><?php echo $array['order_number']; ?></td>
        <th width="10%">订单日期：</th>
        <td width="15%"><?php echo $array['order_date']; ?></td>
        <th width="10%">供应商：</th>
        <td width="15%"><?php echo $array['supplier_cname']; ?></td>
        <th width="10%">操作人：</th>
        <td width="15%"><?php echo $array['employee_name']; ?></td>
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
        <td><?php echo $array['quantity']; ?> 件</td>
        <th>单价：</th>
        <td><?php echo $array['unit_price']; ?></td>
        <th>金额：</th>
        <td><?php echo $array['amount']; ?></td>
      </tr>
      <tr>
        <th>税率：</th>
        <td><?php echo $array['tax_rate']*100 ?>%</td>
        <th>入库日期：</th>
        <td><input type="text" name="dodate" value="<?php echo$array['dodate']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>送货单号：</th>
        <td><input type="text" name="form_number" id="form_number" value="<?php echo $array['form_number']; ?>"  class="input_txt" /></td>
        <th>备注：</th>
        <td><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="inoutid" id="inoutid" value="<?php echo $inoutid; ?>" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php
	  }
  }
  ?>
</div>
<?php
if($action == "add"){
	$sql_in = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`form_number`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`remark`,`db_cutter_inout`.`dotime`,`db_cutter_order_list`.`unit_price`,(`db_cutter_order_list`.`unit_price`*`db_cutter_inout`.`quantity`) AS `amount`,`db_employee`.`employee_name` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_inout`.`listid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_inout`.`employeeid` WHERE `db_cutter_inout`.`listid` = '$listid' AND `db_cutter_inout`.`dotype` = 'I' ORDER BY `db_cutter_inout`.`inoutid` DESC";
	$result_in = $db->query($sql_in);
?>
<div id="table_list">
    <?php if($result_in->num_rows){ ?>
    <table>
      <caption>
      入库记录
      </caption>
      <tr>
        <th width="4%">ID</th>
        <th width="12%">表单号</th>
        <th width="8%">数量</th>
        <th width="6%">单位</th>
        <th width="10%">单价(含税)</th>
        <th width="10%">金额(含税)</th>
        <th width="10%">出入库日期</th>
        <th width="10%">操作人</th>
        <th width="14%">操作时间</th>
        <th width="16%">备注</th>
      </tr>
      <?php
	  $total_quantity = 0;
      while($row_in = $result_in->fetch_assoc()){
	  ?>
      <tr>
        <td><?php echo $row_in['inoutid']; ?></td>
        <td><?php echo $row_in['form_number']; ?></td>
        <td><?php echo $row_in['quantity']; ?></td>
        <td>件</td>
        <td><?php echo $row_in['unit_price']; ?></td>
        <td><?php echo $row_in['amount']; ?></td>
        <td><?php echo $row_in['dodate']; ?></td>
        <td><?php echo $row_in['employee_name']; ?></td>
        <td><?php echo $row_in['dotime']; ?></td>
        <td><?php echo $row_in['remark']; ?></td>
      </tr>
      <?php
      $total_quantity += $row_in['quantity'];
	  }
	  ?>
      <tr>
        <td colspan="2">Total</td>
        <td><?php echo $total_quantity; ?></td>
        <td colspan="7">&nbsp;</td>
      </tr>
    </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无入库记录</p>";
  }
  ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>