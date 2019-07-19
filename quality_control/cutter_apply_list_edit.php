<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/page.php';
require_once 'shell.php';
$apply_listid = fun_check_int($_GET['id']);
$employeeid = $_SESSION['employee_info']['employeeid'];
 $sql_apply = "SELECT `db_cutter_apply_list`.`cutterid`,`db_cutter_apply_list`.`quantity`,`db_cutter_apply_list`.`mouldid`,`db_cutter_apply_list`.`plan_date`,`db_cutter_apply_list`.`remark`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`apply_date`,`db_cutter_apply`.`apply_time`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_employee`.`employee_name`,`db_mould`.`mould_number` FROM `db_cutter_apply_list` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_cutter_apply_list`.`mouldid` WHERE `db_cutter_apply_list`.`apply_listid` = '$apply_listid' AND `db_cutter_apply`.`employeeid` = '$employeeid'";
$result_apply = $db->query($sql_apply);
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
		var quantity = $("#quantity").val();
		if(!ri_b.test(quantity)){
			$("#quantity").focus();
			return false;
		}
		var mouldid = $("#mouldid").val();
		if(!mouldid){
			$("#mould_number").focus();
			return false;
		}
	})
	$("#quantity").blur(function(){
		var default_quantity = this.defaultValue;
		var quantity = $(this).val();
		if(!ri_b.test(quantity)){
			alert('请输入大于零的数字');
			$(this).val(default_quantity);
		}else{
			var cutterid = $("#cutterid").val();
			$.post('../ajax_function/cutter_apply_quantity_check.php',{
				quantity:quantity,
				cutterid:cutterid
			},function(data,textstatus){
				if(data == 0){
					alert('申领数量异常！');
					$("#quantity").val(default_quantity);
				}
			})
		}
	})
	$("#mould_number").keyup(function(){
		var mould_number = $(this).val();
		if($.trim(mould_number)){
			$.post('../ajax_function/mould_try.php',{
				mould_number:mould_number
			},function(data,textstatus){
				$("#mouldid").show();
				$("#mouldid").html(data);
			})
		}else{
			$("#mouldid").hide();
			$("#mouldid").val('');
		}
	})
	$("#mouldid").dblclick(function(){
		var mould_number = $("#mouldid option:selected").text();
		var mouldid = $("#mouldid option:selected").val();
		if(mouldid){
			$("#mould_number").val(mould_number);
			$(this).hide();			
		}
	})
})
</script>
<title>模具刀具-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($result_apply->num_rows){
	$array_apply = $result_apply->fetch_assoc();
	$cutterid = $array_apply['cutterid'];
	$sql_surplus = "SELECT `db_cutter_order_list`.`surplus` FROM `db_cutter_order_list` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_purchase_list`.`cutterid` = '$cutterid' AND `db_cutter_order_list`.`surplus` > 0";
	$result_surplus = $db->query($sql_surplus);
	if($result_surplus->num_rows){
		while($row_surplus = $result_surplus->fetch_assoc()){
			$surplus += $row_surplus['surplus'];
		}
	}else{
		$surplus = 0;
	}
?>
<div id="table_sheet">
  <h4>刀具申领明细修改</h4>
  <form action="cutter_apply_list_editdo.php" name="cutter_apply_list_edit" method="post">
    <table>
      <tr>
        <th width="10%">申领单号：</th>
        <td width="15%"><?php echo $array_apply['apply_number']; ?></td>
        <th width="10%">申领人：</th>
        <td width="15%"><?php echo $array_apply['employee_name']; ?></td>
        <th width="10%">申请日期：</th>
        <td width="15%"><?php echo $array_apply['apply_date']; ?></td>
        <th width="10%">操作时间：</th>
        <td width="15%"><?php echo $array_apply['apply_time']; ?></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><?php echo $array_apply['type']; ?></td>
        <th>规格：</th>
        <td><?php echo $array_apply['specification']; ?></td>
        <th>材质：</th>
        <td><?php echo $array_cutter_texture[$array_apply['texture']]; ?></td>
        <th>硬度：</th>
        <td><?php echo $array_apply['hardness']; ?></td>
      </tr>
      <th>库存：</th>
        <td><?php echo $surplus; ?> 件</td>
        <th>数量：</th>
        <td><input type="text" name="quantity" id="quantity" value="<?php echo $array_apply['quantity']; ?>" class="input_txt" /> 件</td>
        <th>模具：</th>
        <td><input type="text" name="mould_number" id="mould_number" value="<?php echo $array_apply['mould_number']; ?>" class="input_txt" />
          <br />
          <select name="mouldid" id="mouldid" size="4" style="width:140px; border:1px solid #DDD; position:absolute; display:none;">
            <option value="<?php echo $array_apply['mouldid']; ?>" selected="selected"></option>
          </select></td>
        <th>计划领用日期：</th>
        <td><input type="text" name="plan_date" value="<?php echo $array_apply['plan_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>备注：</th>
        <td colspan="7"><input type="text" name="remark" value="<?php echo $array_apply['remark']; ?>" class="input_txt" size="28" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javscript:history.go(-1);" />
          <input type="hidden" name="cutterid" id="cutterid" value="<?php echo $array_apply['cutterid']; ?>" />
          <input type="hidden" name="apply_listid" value="<?php echo $apply_listid; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>