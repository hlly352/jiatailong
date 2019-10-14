<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$inquiry_orderid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
//查找所有的加工类型
$sql_outward_type = "SELECT distinct(`db_outward_inquiry`.`outward_typeid`),`db_mould_outward_type`.`outward_typename` FROM `db_outward_inquiry` INNER JOIN `db_mould_outward_type` ON `db_outward_inquiry`.`outward_typeid` = `db_mould_outward_type`.`outward_typeid`";
$result_outward_type = $db->query($sql_outward_type);
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
  $sql_order = "SELECT `db_outward_inquiry_order`.`inquiry_orderid`,`db_outward_inquiry_order`.`inquiry_number`,`db_outward_inquiry_order`.`inquiry_date`,DATE_ADD(`db_outward_inquiry_order`.`inquiry_date`,interval +`db_outward_inquiry_order`.`delivery_cycle` day) AS `plan_date`,`db_outward_inquiry_order`.`dotime`,`db_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM  `db_outward_inquiry_order` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_outward_inquiry_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_outward_inquiry_order`.`employeeid` WHERE `db_outward_inquiry_order`.`inquiry_orderid` = '$inquiry_orderid'";
  $result_inquiry = $db->query($sql_order);
  if($result_inquiry->num_rows){
	  $array_inquiry = $result_inquiry->fetch_assoc();
	  $plan_date = $array_inquiry['plan_date'];
	  $inquiry_orderid = $array_inquiry['inquiry_orderid'];
  ?>
  <h4>外协加工询价单</h4>
  <table>
    <tr>
      <th width="10%">询价单号：</th>
      <td width="15%"><?php echo $array_inquiry['inquiry_number']; ?></td>
      <th width="10%">询价日期：</th>
      <td width="15%"><?php echo $array_inquiry['inquiry_date']; ?></td>
      <th width="10%">供应商：</th>
      <td width="15%"><?php echo $array_inquiry['supplier_cname']; ?></td>
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
	$id = $orderid;
	$mould_number = trim($_GET['mould_number']);
	$material_number = trim($_GET['material_number']);
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['specification']);
	$outward_typeid = trim($_GET['outward_typeid']);
	if($outward_typeid){
		$str_typeid = "AND `db_outward_inquiry`.`outward_typeid` = '$outward_typeid'";
	}
	$sqlwhere = " AND `db_mould_specification`.`mould_no` LIKE '%$mould_number%' AND `db_mould_material`.`material_number` LIKE '%$material_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%' $str_typeid";
	
}
//查询所有待询价物料信息

$sql = "SELECT `db_employee`.`employee_name`,`db_mould_outward_type`.`outward_typename`,`db_outward_inquiry`.`inquiryid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_material`.`complete_status`,`db_mould_specification`.`mould_no`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code`,`db_outward_inquiry`.`outward_quantity`,`db_outward_inquiry`.`outward_remark` FROM `db_outward_inquiry` INNER JOIN `db_mould_material` ON `db_outward_inquiry`.`materialid` = `db_mould_material`.`materialid` INNER JOIN `db_mould_specification` ON `db_mould_specification`.`mould_specification_id` = `db_mould_material`.`mouldid` INNER JOIN `db_mould_outward_type` ON `db_outward_inquiry`.`outward_typeid` = `db_mould_outward_type`.`outward_typeid` INNER JOIN `db_employee` ON `db_outward_inquiry`.`employeeid` = `db_employee`.`employeeid` WHERE `db_outward_inquiry`.`status` = '0' AND `db_outward_inquiry`.`inquiryid` NOT IN(SELECT `inquiryid` FROM `db_outward_inquiry_orderlist` GROUP BY `inquiryid`) $sqlwhere";
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
        <th>加工类型：</th>
        <td>
	        <select name="outward_typeid" class="input_txt txt">
		        <option value="">所有</option>
		       <?php  
		       	if($result_outward_type->num_rows){
		       		while($row_outward_type = $result_outward_type->fetch_assoc()){
		       ?>
		        <option value="<?php echo $row_outward_type['outward_typeid'] ?>">
		        	<?php echo $row_outward_type['outward_typename'] ?>
		        </option>
		        <?php }} ?>
	        </select>
        </td>
        <td>
        	<input type="submit" name="submit" value="查询" class="button" />
        	<input type="hidden" name="id" value="<?php echo $inquiry_orderid ?>" />
        </td> 
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php
  if($result->num_rows){
  ?>
  <form action="outward_inquiry_order_listdo.php" name="mould_material_list" method="post">
    <table>
      <tr>
        <th width="">ID</th>
        <th width="">模具编号</th>
<!--         <th width="">料单编号</th>
        <th width="">料单序号</th> -->
        <th width="">物料编码</th>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th width="">数量</th>
        <th width="">材质</th>
        <th width="">硬度</th>
        <th width="">品牌</th>
        <th width="">申请人</th>
        <th width="">加工类型</th>
        <th width="">计划回厂时间</th>
        <th width="15%">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  $specification_bg = '';
		  $material_name_bg = '';
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
        </td>
        <td><?php echo $row['mould_no']; ?></td>
<!--         <td><?php echo $row['material_list_number']; ?></td>
        <td><?php echo $row['material_list_sn']; ?></td> -->
        <td><?php echo $row['material_number']; ?></td>
        <td<?php echo $material_name_bg; ?>><?php echo $row['material_name']; ?></td>
        <td<?php echo $specification_bg; ?>><?php echo $specification; ?></td>
        <td><?php echo $row['outward_quantity']; ?></td>
        <td><?php echo $row['texture']; ?></td>
        <td><?php echo $row['hardness']; ?></td>
        <td><?php echo $row['brand']; ?></td>
        <td><?php echo $row['employee_name'];?></td>
        <td><?php echo $row['outward_typename']; ?></td>
        <td>
        	<input type="text" name="plan_date_<?php echo $inquiryid; ?>" value="<?php echo $plan_date ?>" class="input_txt" size="15" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" />
        </td>
        <td>
        	<input type="text" value="<?php echo $row['outward_remark']; ?>" name="outward_remark_<?php echo $inquiryid; ?>" />	
        </td>
      </tr>
      <?php } ?>
      <tr>
      	<td colspan="13">
      		<input type="submit" value="添加" name="submit" class="button" />
      		<input type="hidden" name="inquiry_orderid" value="<?php echo $inquiry_orderid ?>" />
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