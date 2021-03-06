<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$orderid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
//查找所有的询价单信息
$sql_inquiry = "SELECT `db_outward_inquiry_order`.`inquiry_orderid`,`db_outward_inquiry_order`.`inquiry_number`,`db_supplier`.`supplier_cname` FROM `db_outward_inquiry_order` INNER JOIN `db_supplier` ON `db_outward_inquiry_order`.`supplierid` = `db_supplier`.`supplierid` WHERE `db_outward_inquiry_order`.`inquiry_order_status` = '1' AND `db_outward_inquiry_order`.`inquiry_orderid` NOT IN (SELECT `inquiry_orderid` FROM `db_outward_order` GROUP BY `inquiry_orderid`)";

$result_inquiry_order = $db->query($sql_inquiry);
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
		var inquiryid = id.substr(id.lastIndexOf('_')+1);
		if($.trim(unit_price) && !rf_b.test(unit_price)){
			alert('请输入大于零的数字')
			$(this).val(this.defaultValue);
		}else{
			$(this).val(unit_price.toFixed(2));
			var outward_quantity = $('#outward_quantity_'+inquiryid).html();
			var amount = outward_quantity * unit_price;
			$('#amount_'+inquiryid).val(amount.toFixed(2));
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
  $sql_order = "SELECT `db_outward_inquiry_order`.`inquiry_orderid`,`db_outward_inquiry_order`.`inquiry_number`,`db_outward_inquiry_order`.`inquiry_date`,DATE_ADD(`db_outward_inquiry_order`.`inquiry_date`,interval +`db_outward_inquiry_order`.`delivery_cycle` day) AS `plan_date`,`db_outward_inquiry_order`.`dotime`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM  `db_outward_inquiry_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_outward_inquiry_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_outward_inquiry_order`.`employeeid` WHERE `db_outward_inquiry_order`.`inquiry_orderid` = '$inquiry_orderid'";
  $sql_order = "SELECT * FROM `db_outward_order` INNER JOIN `db_employee` ON `db_outward_order`.`employeeid` = `db_employee`.`employeeid` WHERE `db_outward_order`.`orderid` = '$orderid'";
  $result_inquiry = $db->query($sql_order);
  if($result_inquiry->num_rows){
	  $array_inquiry = $result_inquiry->fetch_assoc();
	  $orderid = $array_inquiry['orderid'];
  ?>
  <h4>外协加工订单</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="15%"><?php echo $array_inquiry['order_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo $array_inquiry['order_date']; ?></td>
      <!-- <th width="10%">供应商：</th>
      <td width="15%"><?php echo $array_inquiry['supplier_cname']; ?></td> -->
      <th width="10%">操作人：</th>
      <td width="15%"><?php echo $array_inquiry['employee_name']; ?></td>
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

	$inquiry_orderid = trim($_GET['inquiry_order']);
	
}else{
	if($result_inquiry_order->num_rows){
		$inquiry_orderid = $result_inquiry_order->fetch_assoc()['inquiry_orderid'];
	}
}
$sqlwhere = " WHERE `db_outward_inquiry_order`.`inquiry_orderid` = '$inquiry_orderid' ";
//查询所有待询价物料信息

$sql = "SELECT `db_outward_inquiry_orderlist`.`back_date`,`db_employee`.`employee_name`,`db_mould_outward_type`.`outward_typename`,`db_outward_inquiry`.`inquiryid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_material`.`complete_status`,`db_mould_specification`.`mould_no`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code`,`db_outward_inquiry`.`outward_quantity`,`db_outward_inquiry`.`outward_remark` FROM `db_outward_inquiry_orderlist` INNER JOIN `db_outward_inquiry_order` ON `db_outward_inquiry_order`.`inquiry_orderid` = `db_outward_inquiry_orderlist`.`inquiry_orderid` INNER JOIN `db_outward_inquiry` ON `db_outward_inquiry_orderlist`.`inquiryid` = `db_outward_inquiry`.`inquiryid` INNER JOIN `db_mould_material` ON `db_outward_inquiry`.`materialid` = `db_mould_material`.`materialid` INNER JOIN `db_mould_outward_type` ON `db_outward_inquiry`.`outward_typeid` = `db_mould_outward_type`.`outward_typeid` INNER JOIN `db_mould_specification` ON `db_mould_material`.`mouldid` = `db_mould_specification`.`mould_specification_id` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_outward_inquiry`.`employeeid` $sqlwhere";

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
        <th>询价单：</th>
        <td>
	        <select name="inquiry_order" class="input_txt txt">
	        	<?php 
	        		$result_inquiry_order = $db->query($sql_inquiry);
	        		if($result_inquiry_order->num_rows){
 						while($rows = $result_inquiry_order->fetch_assoc()){
 						
	        	?>
		        <option value="<?php echo $rows['inquiry_orderid'] ?>"><?php echo $rows['supplier_cname'].'-'.$rows['inquiry_number'] ?></option>
		        <?php }} ?>
	        </select>
        </td>
        <td>
        	<input type="submit" name="submit" value="查询" class="button" />
        	<input type="hidden" name="id" value="<?php echo $orderid ?>" />
        	<input type="hidden" name="inquiry_orderid" value="<?php echo $inquiry_order ?>" />
        </td> 
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
  ?>
  <form action="outward_order_listdo.php" name="mould_material_list" method="post">
    <table>
      <tr>
        <th width="">ID</th>
        <th width="">模具编号</th>
<!--         <th width="">料单编号</th>
        <th width="">料单序号</th> -->
        <th width="">物料编码</th>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th width="">材质</th>
        <th width="">硬度</th>
        <th width="">品牌</th>
        <th width="">申请人</th>
        <th width="">加工类型</th>
        <th width="">回厂时间</th>
        <th width="">数量</th>
        <th width="">单价</th>
        <th width="">金额</th>
        <th width="15%">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $inquiryid = $row['inquiryid'];
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
        	<input type="checkbox" name="inquiryid[]" value="<?php echo $inquiryid; ?>"<?php //if(in_array($materialid,$array_order)) echo " disabled=\"disabled\""; ?> />
        	<input type="hidden" name="inquiryid[]" value="<?php echo $inquiryid ?>" />
        </td>
        <td><?php echo $row['mould_no']; ?></td>
<!--         <td><?php echo $row['material_list_number']; ?></td>
        <td><?php echo $row['material_list_sn']; ?></td> -->
        <td><?php echo $row['material_number']; ?></td>
        <td<?php echo $material_name_bg; ?>><?php echo $row['material_name']; ?></td>
        <td<?php echo $specification_bg; ?>><?php echo $specification; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['hardness']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['employee_name'];?></td>
        <td><?php echo $row['outward_typename']; ?></td>
        <td><?php echo $row['back_date']; ?></td>
        <td id="outward_quantity_<?php echo $inquiryid ?>"><?php echo $row['outward_quantity']; ?></td>
        <td>
        	<input type="text" id="unit_price_<?php echo $inquiryid; ?>" name="unit_price[]" class="input_txt" size="10" />
        </td>
        <td>
        	<input type="text" name="amount[]" class="input_txt" size="10" id="amount_<?php echo $inquiryid; ?>" />
        </td>
        <td><?php echo $row['outward_remark']; ?></td>
      </tr>
      <?php } ?>
      <tr>
      	<td colspan="15">
      		<input type="submit" value="添加" name="submit" class="button" />
      		<input type="hidden" name="inquiry_orderid" value="<?php echo $inquiry_orderid ?>" />
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