<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['id']);
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
	$("input[name^=order_quantity]").blur(function(){
		var order_quantity = $(this).val();
		if(!rf_b.test(order_quantity)){
			alert('请输入大于零的数字');
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2));
			var array_id = $(this).attr('id').split('-');
			var materialid = array_id[1];
			$.post('../ajax_function/material_order_quantity.php',{
			materialid:materialid
			},function(data,textstatus){
				var unit_price = $("#unit_price-"+materialid).val();
				var actual_quantity = order_quantity*data;
				$("#actual_quantity-"+materialid).val(parseFloat(actual_quantity).toFixed(2));
				var amount = actual_quantity*unit_price;
				$("#amount-"+materialid).val(amount.toFixed(2));
			})
		}
	})
	*/
	//失去焦点
	$("input[name^=actual_quantity]").blur(function(){
		var actual_quantity = $(this).val();
		if(!rf_a.test(actual_quantity) && $.trim(actual_quantity)){
			alert('请输入数字');
			$(this).val(this.defaultValue);
		}else{
			if($.trim(actual_quantity)){
				$(this).val(parseFloat($(this).val()).toFixed(2));
				var array_id = $(this).attr('id').split('-');
				var materialid = array_id[1];
				var unit_price = $("#unit_price-"+materialid).val();
				var amount = actual_quantity*unit_price;
				$("#amount-"+materialid).val(amount.toFixed(2));
			}
		}
	})
	//获取焦点
	$("input[name^=order_quantity]").live('focus',function(){
		var quantity = $(this).parent().prev().html();
		$(this).val(quantity);
		// var array_id = $(this).attr('id').split('-');
		// var materialid = array_id[1];
		// $.post('../ajax_function/material_order_quantity.php',{
		// 	materialid:materialid
		// },function(data,textstatus){
		// 	var array_data = data.split('#');
		// 	var actual_quantity = array_data[0];	
		// 	var unitid = array_data[1];
		// 	$("#actual_quantity-"+materialid).val(actual_quantity);
		// 	$("#actual_unitid-"+materialid).find("option[value="+unitid+"]").attr("selected",true);
		// 	var unit_price = $("#unit_price-"+materialid).val();
		// 	var amount = actual_quantity*unit_price;
		// 	$("#amount-"+materialid).val(amount.toFixed(2));

		// })
	})
	$('input[name ^= order_quantity]').live('blur',function(){
		var order_quantity = parseFloat($(this).val());
		var id = $(this).attr('id');
		var materialid = id.substr(id.lastIndexOf('_')+1);
		if($.trim(order_quantity) && !rf_b.test(order_quantity)){
			alert('请输入大于零的数字')
			$(this).val(this.defaultValue);
		}else{
			$(this).val(order_quantity.toFixed(2));
			var unit_price = $("#unit_price_"+materialid).val();
			var amount = order_quantity * unit_price;
			$("#amount_"+materialid).val(amount.toFixed(2));
		}
	})							 
	$("input[name^=unit_price]").blur(function(){
		var unit_price = parseFloat($(this).val());
		var id = $(this).attr('id');
		var materialid = id.substr(id.lastIndexOf('_')+1);
		if($.trim(unit_price) && !rf_b.test(unit_price)){
			alert('请输入大于零的数字')
			$(this).val(this.defaultValue);
		}else{
			$(this).val(unit_price.toFixed(2));
			var order_quantity = $('#order_quantity_'+materialid).val();
			var amount = order_quantity * unit_price;
			$('#amount_'+materialid).val(amount.toFixed(2));
		}
	})

	
	$("input[name^=amount]").blur(function(){
		var amount = $(this).val();
		if(!rf_a.test(amount)){
			alert('请输入数字')
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2))
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
  $sql_order = "SELECT `db_outward_order`.`order_number`,`db_outward_order`.`order_date`,DATE_ADD(`db_outward_order`.`order_date`,interval +`db_outward_order`.`delivery_cycle` day) AS `plan_date`,`db_outward_order`.`dotime`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_outward_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_outward_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_outward_order`.`employeeid` WHERE `db_outward_order`.`orderid` = '$orderid' AND `db_outward_order`.`employeeid` = '$employeeid'";
 
  $result_order = $db->query($sql_order);
  if($result_order->num_rows){
	  $array_order = $result_order->fetch_assoc();
	  $plan_date = $array_order['plan_date'];
  ?>
  <h4>外协加工订单</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="15%"><?php echo $array_order['order_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo $array_order['order_date']; ?></td>
      <th width="10%">供应商：</th>
      <td width="15%"><?php echo $array_order['supplier_cname']; ?></td>
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_order['employee_name']; ?></td>
    </tr>
  </table>
  <?php
  }else{
	  die("<p class=\"tag\">系统提示：暂无记录！</p></div>");
  }
  ?>
</div>
<?php
if($_GET['submit']){
	$id = $orderid;
	$mould_number = trim($_GET['mould_number']);
	$material_number = trim($_GET['material_number']);
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['specification']);
	$complete_status = $_GET['complete_status'];
	if($complete_status != NULL){
		$sql_complete_status = " AND `db_mould_material`.`complete_status` = '$complete_status'";
	}
	$sqlwhere = " WHERE `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_number` LIKE '%$material_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' $sql_complete_status";
	
}
$sql = "SELECT `db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_material`.`complete_status`,`db_mould`.`mould_number`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code` FROM `db_mould_material` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` $sqlwhere";

$result = $db->query($sql);
$result_id = $db->query($sql);
$_SESSION['mould_material_list'] = $sql;
$pages = new page($result->num_rows,15);
$sqllist = $sql . " ORDER BY `db_mould_material`.`material_date` DESC,`db_mould_material`.`materialid` DESC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>模具物料</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>模具编号：</th>
        <td><input type="text" name="mould_number" class="input_txt" /></td>
        <th>物料编号：</th>
        <td><input type="text" name="material_number" class="input_txt" /></td>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="specification" class="input_txt" /></td>
        <th>完整：</th>
        <td><select name="complete_status">
        <option value="">所有</option>
        <?php
        foreach($array_is_status as $is_status_key=>$is_status_value){
			echo "<option value=\"".$is_status_key."\">".$is_status_value."</option>";
		}
		?>
        </select></td>
        <td>
        	<input type="submit" name="submit" value="查询" class="button" />
        	<input type="hidden" name="id" value="<?php echo $orderid ?>" />
        </td> 
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
  ?>
  <form action="mould_outward_listdo.php" name="mould_material_list" method="post">
    <table>
      <tr>
        <th width="">ID</th>
        <th width="">模具编号</th>
        <th width="">料单编号</th>
        <th width="">料单序号</th>
        <th width="">物料编码</th>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th width="">材质</th>
        <th width="">数量</th>
        <th width="">加工数量</th>
        <th width="">单价</th>
        <th width="">金额</th>
        <th width="15%">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $specification_bg = '';
		  $material_name_bg = '';
		  $materialid = $row['materialid'];
		  $material_number_code = $row['material_number_code'];
		  $specification = $row['specification'];
		  if(in_array($material_number_code,array(1,2,3,4,5))){
			  $tag_a = substr_count($specification,'*');
			  $tag_b = substr_count($specification,'#');
			  $specification_bg = ($tag_a != 2 || $tag_b != 1)?" style=\"background:orange\"":'';
		  }
		  $material_name_bg = $row['complete_status']?'':" style=\"background:yellow\"";
	  ?>
      <tr>
        <td>
        	<?php echo $materialid ?>
        	<input type="hidden" name="materialid[]" value="<?php echo $materialid; ?>"<?php //if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> />
        </td>
        <td><?php echo $row['mould_number']; ?></td>
        <td><?php echo $row['material_list_number']; ?></td>
        <td><?php echo $row['material_list_sn']; ?></td>
        <td><?php echo $row['material_number']; ?></td>
        <td<?php echo $material_name_bg; ?>><?php echo $row['material_name']; ?></td>
        <td<?php echo $specification_bg; ?>><?php echo $specification; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['material_quantity']; ?></td>
        <td>
        	<input type="text" name="order_quantity[]" id="order_quantity_<?php echo $materialid; ?>" class="input_txt order_quantity" size="8"/>
        </td>
        <td>
        	<input type="text" name="unit_price[]" id="unit_price_<?php echo $materialid; ?>" class="input_txt unit_price"  size="8" />
        </td>
        <td>
        	<input type="text" readonly name="amount[]" id="amount_<?php echo $materialid; ?>" class="input_txt amount" size="10" />
        </td>
        <td><?php echo $row['remark']; ?></td>
      </tr>
      <?php } ?>
      <tr>
      	<td colspan="13">
      		<input type="submit" value="添加" name="submit" class="button" />
      		<input type="hidden" name="orderid" value="<?php echo $orderid ?>" />
      		<input type="button" value="返回" class="button" onclick="window.history.go(-1)" />
      	</td>
      </tr>
    </table>
   <!--  <div id="checkall">
      <input name="all" type="button" class="select_button" id="CheckedAll" value="全选" />
      <input type="button" name="other" class="select_button" id="CheckedRev" value="反选" />
      <input type="button" name="reset" class="select_button" id="CheckedNo" value="清除" />
      <input type="submit" name="submit" id="submit" value="添加" class="select_button" disabled="disabled" />
      <input type="hidden" name="action" value="del" />
    </div> -->
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>