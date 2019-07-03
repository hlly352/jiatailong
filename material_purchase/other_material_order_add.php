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
	$("input[name^=actual_quantity]").focus(function(){
		var array_id = $(this).attr('id').split('-');
		var materialid = array_id[1];
		$.post('../ajax_function/material_order_quantity.php',{
			materialid:materialid
		},function(data,textstatus){
			var array_data = data.split('#');
			var actual_quantity = array_data[0];	
			var unitid = array_data[1];
			$("#actual_quantity-"+materialid).val(actual_quantity);
			$("#actual_unitid-"+materialid).find("option[value="+unitid+"]").attr("selected",true);
			var unit_price = $("#unit_price-"+materialid).val();
			var amount = actual_quantity*unit_price;
			$("#amount-"+materialid).val(amount.toFixed(2));
		})
	})								 
	$("input[name^=unit_price]").blur(function(){
		var unit_price = $(this).val();
		if($.trim(unit_price) && !rf_b.test(unit_price)){
			alert('请输入大于零的数字')
			$(this).val(this.defaultValue);
		}else{
			if($.trim(unit_price)){
				$(this).val(parseFloat($(this).val()).toFixed(2));
				var array_id = $(this).attr('id').split('-');
				var materialid = array_id[1];
				var actual_quantity = $("#actual_quantity-"+materialid).val();
				var amount = actual_quantity*unit_price;
				$("#amount-"+materialid).val(amount.toFixed(2));
			}
		}
	})

	/*
	$("input[name^=amount]").blur(function(){
		var amount = $(this).val();
		if(!rf_a.test(amount)){
			alert('请输入数字')
			$(this).val(this.defaultValue);
		}else{
			$(this).val(parseFloat($(this).val()).toFixed(2))
		}
	})
	*/
	$("#data_source").change(function(){
		$("#submit").click();
	})
})
</script>
<title>采购管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  $sql_order = "SELECT `db_other_material_order`.`order_number`,`db_other_material_order`.`order_date`,DATE_ADD(`db_other_material_order`.`order_date`,interval +`db_other_material_order`.`delivery_cycle` day) AS `plan_date`,`db_other_material_order`.`dotime`,`db_other_supplier`.`supplier_cname`,`db_employee`.`employee_name` FROM `db_other_material_order` INNER JOIN `db_other_supplier` ON `db_other_supplier`.`other_supplier_id` = `db_other_material_order`.`supplierid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_other_material_order`.`employeeid` WHERE `db_other_material_order`.`orderid` = '$orderid' AND `db_other_material_order`.`employeeid` = '$employeeid'";
  $result_order = $db->query($sql_order);
  if($result_order->num_rows){
	  $array_order = $result_order->fetch_assoc();
	  $plan_date = $array_order['plan_date'];
  ?>
  <h4>物料订单</h4>
  <table>
    <tr>
      <th width="10%">合同号：</th>
      <td width="15%"><?php echo $array_order['order_number']; ?></td>
      <th width="10%">订单日期：</th>
      <td width="15%"><?php echo date('Y-m-d',strtotime($array_order['order_date'])); ?></td>
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
$data_source = $_GET['data_source']?trim($_GET['data_source']):'A';
if($_GET['submit']){
	$material_name = trim($_GET['material_name']);
	$specification = trim($_GET['material_specification']);
	$sqlwhere = " AND `db_mould`.`mould_number` LIKE '%$mould_number%' AND `db_mould_material`.`material_name` LIKE '%$material_name%' AND `db_mould_material`.`specification` LIKE '%$specification%'";
}
if($data_source == 'A'){
	//$sql = "SELECT `db_mould_material`.`materialid`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`complete_status`,`db_mould`.`mould_number` FROM `db_material` INNER JOIN `db_mould_material` ON `db_mould_material`.`materialid` = `db_material_inquiry`.`materialid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_material`.`mouldid` WHERE `db_mould_material`.`materialid` NOT IN (SELECT `materialid` FROM `db_material_order_list` GROUP BY `materialid`) AND `db_material_inquiry`.`employeeid` = '$employeeid' $sqlwhere";
	$sql = "SELECT * FROM `db_mould_other_material` WHERE `inquiryid` = '$employeeid' AND `status` = 'D'";
}elseif($data_source == 'B'){
	$sql = "SELECT * FROM `db_mould_other_material` WHERE `status` = 'D'";
}elseif($data_source == 'C'){
	$sql = "SELECT * FROM `db_mould_other_material` WHERE `status` = 'D' OR `status` = 'B'";
}
$result = $db->query($sql);
$pages = new page($result->num_rows,10);
$sqllist = $sql . " ORDER BY `db_mould_other_material`.`mould_other_id` ASC" . $pages->limitsql;
$result = $db->query($sqllist);
?>
<div id="table_search">
  <h4>可下订单物料</h4>
  <form action="" name="search" method="get">
    <table>
      <tr>
        <th>物料名称：</th>
        <td><input type="text" name="material_name" class="input_txt" /></td>
        <th>规格：</th>
        <td><input type="text" name="material_specification" class="input_txt" /></td>
        <th>物料来源：</th>
        <td><select name="data_source" id="data_source">
            <option value="A"<?php if($data_source == 'A') echo " selected=\"selected\""; ?>>我的询价单</option>
            <option value="B"<?php if($data_source == 'B') echo " selected=\"selected\""; ?>>所有询价单</option>
            <option value="C"<?php if($data_source == 'C') echo " selected=\"selected\""; ?>>未下订单</option>
          </select></td>
        <td><input type="submit" name="submit" id="submit" value="查询" class="button" />
          <input type="button" name="button" value="明细" class="button" onclick="location.href='material_order_list.php?id=<?php echo $orderid; ?>'" />
          <input type="hidden" name="id" value="<?php echo $orderid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<div id="table_list">
  <?php if($result->num_rows){ ?>
  <form action="other_order_material_listdo.php" name="material_list" method="post">
    <table>
      <tr>
        <th width="">模具编号</th>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th >需求数量</th>
        <th >实际数量</th>
        <th>单位</th>
        <th>申请人</th>
        <th>申请部门</th>
        <th width="">单价(含税)</th>
        <th width="">税率</th>
        <th width="">金额(含税)</th>
        <th width="">现金</th>
        <th width="">计划回厂日期</th>
        <th width="">备注</th>
      </tr>
      
      <?php
      while($row = $result->fetch_assoc()){
		  //获取申请部门
		  $dept_sql = "SELECT `dept_name` FROM `db_department` WHERE `deptid`=".$row['apply_team'];
		  $res_dept = $db->query($dept_sql);
		  if($res_dept->num_rows){
		  	$department = $res_dept->fetch_row()[0];
 		  }
	  ?>
      <tr>
        <td><?php echo $row['mould_no']; ?></td>
        <td<?php echo $material_name_bg; ?>><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['material_specification']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td>
        	<input type="text" name="actual_quantity[]" value="<?php echo $row['material_quantity']; ?>" class="input_txt" size="8" />
        </td>
        <td><?php echo $row['unit'] ?></td>
        <td>
        	<?php
        		echo getName($row['applyer'],$db);
        	?>
        </td>
        <td>
			<?php
				echo $department;
			?>
        </td>
        <td>
        	<input type="text" name="unit_price[]" class="input_txt" size="8"/>
        </td>
        <td><select name="tax_rate[]" id="tax_rate-<?php echo $materialid; ?>">
            <?php
			foreach($array_tax_rate as $tax_rate){
				echo "<option value=\"".$tax_rate."\">".($tax_rate*100)."%</option>";
			}
			?>
          </select></td>
        <td>
        	<input type="text" name="amount[]" id="amount-<?php echo $materialid; ?>" class="input_txt" size="10" readonly="readonly" />
        </td>
    
        <td><select name="iscash[]">
            <?php foreach($array_is_status as $is_status_key=>$is_status_value){ ?>
            <option value="<?php echo $is_status_key; ?>"<?php if($is_status_key == 0) echo " selected=\"selected\""; ?>><?php echo $is_status_value; ?></option>
            <?php } ?>
          </select></td>
        <td>
        	<input type="text" name="plan_date[]" value="<?php echo date('Y-m-d',strtotime($plan_date)); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" />
        </td>
        <td><input type="text" name="remark[]" class="input_txt" size="12" />
          <input type="hidden" name="materialid[]" value="<?php echo $row['mould_other_id']; ?>" /></td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="15"><input type="submit" name="submit" value="添加" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="orderid" value="<?php echo $orderid; ?>" />
		  <input type="hidden" name="mould_other_id" value="<?php echo $row['mould_other_id'] ?>"/>
        </td>
      </tr>
    </table>
  </form>
  <div id="page">
    <?php $pages->getPage();?>
  </div>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无物料记录</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>