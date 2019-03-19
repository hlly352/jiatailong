<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_id = $_POST['id'];
	$array_id = fun_convert_checkbox($array_id);
	$sql = "SELECT `db_material_order_list`.`listid`,`db_material_order_list`.`order_surplus`,`db_material_order`.`order_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`texture`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_unit`.`unit_name` FROM `db_material_order_list` INNER JOIN `db_material_order` ON `db_material_order`.`orderid` = `db_material_order_list`.`orderid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_material_order`.`supplierid` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_order_list`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` INNER JOIN `db_unit` ON `db_unit`.`unitid` = `db_material_order_list`.`unitid` WHERE `db_material_order`.`order_status` = 1 AND `db_material_order_list`.`order_surplus` >0 AND `db_material_order_list`.`listid` IN ($array_id)";
	$result = $db->query($sql);
}
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
	$("input[name^=quantity]").blur(function(){
		var default_quantity = this.defaultValue;
		var quantity = $(this).val();
		if(!rf_b.test(quantity)){
			alert('请输入大于零的数字');
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2))
			var array_id = $(this).attr('id').split('-');
			var listid = array_id[1];
			$("#inout_quantity-"+listid).val(parseFloat(quantity).toFixed(2));
			$.post('../ajax_function/material_out_quantity_check.php',{
				quantity:quantity,
				listid:listid
			},function(data,textstatus){
				if(data !=0){
					$("#amount-"+listid).val(data);
				}else{
					alert('出库数量异常！');
					$("#quantity-"+listid).val(default_quantity);
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
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="material_batch_outdo.php" name="material_batch_out" method="post">
    <table>
      <caption>
      订单物料批量出库
      </caption>
      <tr>
        <th width="8%">合同号</th>
        <th width="8%">模具编号</th>
        <th width="12%">物料名称</th>
        <th width="14%">规格</th>
        <th width="8%">材质</th>
        <th width="8%">供应商</th>
        <th width="8%">出库日期</th>
        <th width="8%">出库单号</th>
        <th width="10%">出库数量</th>
        <th width="8%">领料人<span class="tag">*</span></th>
        <th width="8%">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $listid = $row['listid'];
	  ?>
      <tr>
        <td><?php echo $row['order_number']; ?></td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['supplier_cname']; ?></td>
        <td><input type="text" name="dodate[]" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" /></td>
        <td><input type="text" name="form_number[]" class="input_txt" size="12" /></td>
        <td><input type="text" name="quantity[]" id="quantity-<?php echo $listid; ?>" class="input_txt" value="<?php echo $row['order_surplus']; ?>" size="14" />
          <?php echo $row['unit_name']; ?></td>
        <td><input type="text" name="taker[]" class="input_txt" size="12" /></td>
        <td><input type="text" name="remark[]" class="input_txt" size="12" />
          <input type="hidden" name="listid[]" value="<?php echo $listid; ?>" /></td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="11"><input type="submit" name="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" /></td>
      </tr>
    </table>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>