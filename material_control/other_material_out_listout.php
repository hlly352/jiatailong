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
			var quantity = $("#inout_quantity").val();
			if(!rf_b.test(quantity)){
				$("#inout_quantity").focus();
				return false;
			}
		}
		var taker = $("#taker").val();
		if(!$.trim(taker)){
			$("#taker").focus();
			return false;
		}
	})
	$("#inout_quantity").blur(function(){
		var default_quantity = this.defaultValue;
		var quantity = $(this).val();
		var listid = $("#listid").val();
		if(!rf_b.test(quantity)){
			alert('请输入大于零的数字');
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2))
			var inout_quantity = parseFloat($("#stock_quantity").val());
			if(quantity > inout_quantity){
        alert('出库数量异常');

      }
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
	  $sql = "SELECT * FROM `db_other_material_orderlist` INNER JOIN `db_other_material_order` ON `db_other_material_order`.`orderid` = `db_other_material_orderlist`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_other_material_order`.`supplierid` INNER JOIN `db_mould_other_material` ON `db_mould_other_material`.`mould_other_id` = `db_other_material_orderlist`.`materialid` INNER JOIN `db_other_material_data` ON `db_mould_other_material`.`material_name` = `db_other_material_data`.`dataid`  WHERE `db_other_material_orderlist`.`listid` = '$listid'";

	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>订单物料出库</h4>
  <form action="other_material_outlist_outdo.php" name="other_material_outlist_outdo" method="post">
    <table>
      <tr>
        <th width="10%">合同号：</th>
        <td width="15%"><?php echo $array['order_number']; ?></td>
        <th width="10%">物料名称：</th>
        <td width="15%"><?php echo $array['material_name']; ?></td>
        <th width="10%">规格：</th>
        <td width="15%"><?php echo $array['material_specification']; ?></td>
        <th>供应商：</th>
        <td><?php echo $array['supplier_cname']; ?></td>
      </tr>
      <tr>
        <th>出库日期：</th>
        <td><input type="text" name="dodate" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>库存数量</th>
        <td>
          <input type="text" readonly id="stock_quantity" value="<?php echo $array['in_quantity'] ?>" />
        </td>
        <th>出库数量：</th>
        <td><input type="text" name="inout_quantity" id="inout_quantity"  class="input_txt" />
          <?php echo $array['unit_name']; ?></td>
         <th>出库单号：</th>
        <td><input type="text" name="form_number" id="form_number" class="input_txt" /></td>
      </tr>
      <tr>
        <th>领料人：</th>
        <td><input type="text" name="taker" id="taker" class="input_txt" />
        <span class="tag">*</span></td>
        <th>备注：</th>
        <td colspan="3"><input type="text" name="remark" class="input_txt" /></td>
      </tr>
      <tr>
        <td colspan="8" style="text-align:center"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="hidden" name="dataid" value="<?php echo $array['dataid'] ?>"> 
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="inoutid" id="inoutid" value="<?php echo $_GET['inoutid']; ?>" />
          <input type="hidden" name="listid" id="listid" value="<?php echo $listid ?>" />
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
	  $sql = "SELECT `db_material_inout`.`dodate`,`db_material_inout`.`form_number`,`db_material_inout`.`quantity`,`db_material_inout`.`taker`,`db_material_inout`.`remark`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name` FROM `db_material_inout` INNER JOIN `db_material_order_list` ON `db_material_order_list`.`listid` = `db_material_inout`.`listid` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` WHERE `db_material_inout`.`inoutid` = '$inoutid' AND `db_material_inout`.`dotype` = 'O'";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
  ?>
  <h4>订单物料出库修改</h4>
  <form action="material_out_list_outdo.php" name="material_out_list_out" method="post">
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
        <th>出库日期：</th>
        <td><input type="text" name="dodate" value="<?php echo $array['dodate']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <th>出库单号：</th>
        <td><input type="text" name="form_number" id="form_number" value="<?php echo $array['form_number']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>出库数量：</th>
        <td><?php echo $array['quantity'].$array['order_unit_name']; ?></td>
        <th>领料人：</th>
        <td><input type="text" name="taker" id="taker" value="<?php echo $array['taker']; ?>" class="input_txt" />
        <span class="tag">*</span></td>
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
<?php include "../footer.php"; ?>
</body>
</html>