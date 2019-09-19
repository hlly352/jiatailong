<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_POST['submit']){
	$array_id = $_POST['id'];
	$array_id = fun_convert_checkbox($array_id);
	$sql = "SELECT `db_other_material_data`.`material_typeid`,`db_other_material_specification`.`specificationid`,`db_other_material_type`.`material_typename`,`db_other_material_data`.`material_name`,`db_other_material_specification`.`specification_name`,`db_other_material_data`.`unit`,(`db_other_material_specification`.`standard_stock` - `db_other_material_specification`.`stock`) AS `apply_num` FROM `db_other_material_specification` INNER JOIN `db_other_material_data` ON `db_other_material_specification`.`materialid` = `db_other_material_data`.`dataid` INNER JOIN `db_other_material_type` ON `db_other_material_data`.`material_typeid` = `db_other_material_type`.`material_typeid` WHERE `db_other_material_specification`.`specificationid` IN($array_id)";
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
	$("input[name^=inout_quantity]").blur(function(){	
		var inout_quantity = $(this).val();
		if(!rf_b.test(inout_quantity)){
			alert('请输入大于零的数字');
			$(this).val(this.defaultValue);
		}else{
			var array_id = $(this).attr('id').split('-');
			var listid = array_id[1];
			$(this).val(parseFloat($(this).val()).toFixed(2));
			var unit_price = $("#unit_price-"+listid).val();
			var amount = (inout_quantity*unit_price).toFixed(2);
			$("#amount-"+listid).val(amount);
		}
	})
	$("input[name^=amount]").blur(function(){	
		var amount = $(this).val();
		if(!rf_b.test(amount)){
			alert('请输入数字');
			$(this).val(this.defaultValue);
		}else{
			var array_id = $(this).attr('id').split('-');
			var listid = array_id[1];
			$(this).val(parseFloat($(this).val()).toFixed(2));
		}
	})
	$("input[name^=process_cost]").blur(function(){	
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
	$("input[name^=quantity]").blur(function(){
		var default_quantity = this.defaultValue;
		var quantity = $(this).val();
		if(!rf_b.test(quantity)){
			alert('请输入大于零的数字');
			$(this).val(this.defaultValue);
		}
	})
})
</script>
<title>物控管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_list">
  <?php
  if($result->num_rows){
	  $sql_in_quantity = "SELECT `listid`,SUM(`inout_quantity`) AS `in_quantity` FROM `db_material_inout` WHERE `dotype` = 'I' AND `listid` IN ($array_id) GROUP BY `listid`";
	  $result_in_quantity = $db->query($sql_in_quantity);
	  if($result_in_quantity->num_rows){
		  while($row_in_quantity = $result_in_quantity->fetch_assoc()){
			  $array_in_quantity[$row_in_quantity['listid']] = $row_in_quantity['in_quantity'];
		  }
	  }else{
		  $array_in_quantity = array();
	  }
  ?>
  <form action="mould_other_materialdo.php" name="" method="post">
    <table>
      <caption>
      期间物料批量申购
      </caption>
      <tr>
        <th width="">物料类型</th>
        <th width="">物料名称</th>
        <th width="">规格</th>
        <th width="">申购数量</th>
        <th width="">单位</th>
        <th width="">申购人</th>
        <th width="">申购部门</th>
        <th width="">申购日期</th>
        <th width="">需求日期</th>
        <th width="">备注</th>
      </tr>
      <?php
      while($row = $result->fetch_assoc()){
		  //查询申购人的姓名和部门
		  $applyer_sql = "SELECT `db_employee`.`employee_name`,`db_department`.`dept_name`,`db_department`.`deptid` FROM `db_employee` INNER JOIN `db_department` ON `db_employee`.`deptid` = `db_department`.`deptid` WHERE `db_employee`.`employeeid` = '$employeeid'";
		  $result_applyer = $db->query($applyer_sql);
		  if($result_applyer->num_rows){
		  	$applyer = $result_applyer->fetch_assoc();
		  }
	  ?>
      <tr>
        <td><?php echo $row['material_typename']; ?></td>
        <td><?php echo $row['material_name']; ?></td>
        <td><?php echo $row['specification_name']; ?></td>
        <td><input type="text" name="quantity[]" id="unit_price-<?php echo $listid; ?>" class="input_txt" value="<?php echo $row['apply_num']; ?>" size="10" /></td>
        <td><?php echo $row['unit']; ?></td>
        <td><?php echo $applyer['employee_name']; ?></td>
        <input type="hidden" name="applyer[]" value="<?php echo $employeeid ?>" />
        <input type="hidden" name="apply_team[]" value="<?php echo $applyer['deptid'] ?>" />
        <input type="hidden" name="material_typeid[]" value="<?php echo $row['material_typeid'] ?>" />
        <td><?php echo $applyer['dept_name']; ?></td>
        <td><input type="text" name="apply_date[]" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" />
        </td>
        <td><input type="text" name="requirement_date[]" value="<?php echo date('Y-m-d',strtotime("+3 days")); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" size="12" />
        </td>
        <td><input type="text" name="remark[]" class="input_txt" size="12" />
          <input type="hidden" name="specificationid[]" value="<?php echo $row['specificationid']; ?>" /></td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="14">
          <input type="submit" name="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="action" value="material_control" />
        </td>
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