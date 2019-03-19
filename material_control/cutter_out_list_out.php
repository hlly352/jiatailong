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
	$("input[name^=quantity]").blur(function(){
		var default_quantity = this.defaultValue;
		var quantity = $(this).val();
		if(!ri_a.test(quantity)){
			alert('请输入大于零的数字');
			$(this).val(default_quantity);
		}else{
			var array_id = $(this).attr('id').split('-');
			var listid = array_id[1];
			$.post('../ajax_function/cutter_out_quantity_check.php',{
				quantity:quantity,
				listid:listid
			},function(data,textstatus){
				if(data == 0){
					alert('出库数量异常！');
					$("#quantity-"+listid).val(default_quantity);
				}else{
					$("#old_quantity-"+listid).val(quantity);
				}
			})
		}
	})
	$("input[name^=old_quantity]").blur(function(){
		var default_old_quantity = this.defaultValue;
		var old_quantity = $(this).val();
		if(!ri_a.test(old_quantity)){
			alert('请输入大于零的数字');
			var array_id = $(this).attr('id').split('-');
			var listid = array_id[1];
			var quantity = $("#quantity-"+listid).val();
			if($.trim(quantity)){
				$(this).val(quantity);
			}else{
				$(this).val(default_old_quantity);
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
	  $apply_listid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_cutter_apply_list`.`cutterid`,(`db_cutter_apply_list`.`quantity`-`db_cutter_apply_list`.`out_quantity`) AS `quantity`,`db_cutter_apply_list`.`plan_date`,`db_cutter_apply_list`.`remark`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`apply_date`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_mould`.`mould_number`,`db_employee`.`employee_name` FROM `db_cutter_apply_list` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_cutter_apply_list`.`mouldid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` WHERE `db_cutter_apply_list`.`apply_listid` = '$apply_listid' AND (`db_cutter_apply_list`.`quantity`-`db_cutter_apply_list`.`out_quantity`) > 0";
	  $result = $db->query($sql);
	  if($result->num_rows){
		  $array = $result->fetch_assoc();
		  $cutterid = $array['cutterid'];
		  $total_out_quantity = $array['quantity'];
  ?>
  <h4>申领刀具出库</h4>
    <table>
      <tr>
        <th width="10%">申领单号：</th>
        <td width="15%"><?php echo $array['apply_number']; ?></td>
        <th width="10%">申领人：</th>
        <td width="15%"><?php echo $array['employee_name']; ?></td>
        <th width="10%">申请日期：</th>
        <td width="15%"><?php echo $array['apply_date']; ?></td>
        <th width="10%">计划申领日期：</th>
        <td width="15%"><?php echo $array['plan_date']; ?></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><?php echo $array['type']; ?></td>
        <th>规格：</th>
        <td><?php echo $array['specification']; ?></td>
        <th>材质：</th>
        <td><?php echo $array_cutter_texture[$array['texture']]; ?></td>
        <th>硬度：</th>
        <td><?php echo $array['hardness']; ?></td>
      </tr>
      <tr>
        <th>模具编号：</th>
        <td><?php echo $array['mould_number']; ?></td>
        <th>出库数量：</th>
        <td colspan="5"><?php echo $array['quantity']; ?> 件</td>
    </table>
  <?php
	  }
  }elseif($action == "edit"){
	  $employeeid = $_SESSION['employee_info']['employeeid'];
	  $inoutid = fun_check_int($_GET['id']);
	  $sql = "SELECT `db_cutter_apply_list`.`plan_date`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`old_quantity`,`db_cutter_inout`.`dodate`,`db_cutter_inout`.`remark`,`db_cutter_apply`.`apply_number`,`db_cutter_apply`.`apply_date`,`db_cutter_type`.`type`,`db_cutter_specification`.`specification`,`db_cutter_hardness`.`texture`,`db_cutter_hardness`.`hardness`,`db_employee`.`employee_name`,`db_mould`.`mould_number` FROM `db_cutter_inout` INNER JOIN `db_cutter_apply_list` ON `db_cutter_apply_list`.`apply_listid` = `db_cutter_inout`.`apply_listid` INNER JOIN `db_cutter_apply` ON `db_cutter_apply`.`applyid` = `db_cutter_apply_list`.`applyid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_apply_list`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_cutter_specification`.`specificationid` = `db_mould_cutter`.`specificationid` INNER JOIN `db_cutter_type` ON `db_cutter_type`.`typeid` = `db_cutter_specification`.`typeid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cutter_apply`.`employeeid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_cutter_apply_list`.`mouldid` WHERE `db_cutter_inout`.`inoutid` = '$inoutid' AND `db_cutter_inout`.`dotype` = 'O' AND `db_cutter_inout`.`employeeid` = '$employeeid'";
	$result = $db->query($sql);
	if($result->num_rows){
		$array = $result->fetch_assoc();
  ?>
  <h4>订单刀具出库修改</h4>
  <form action="cutter_out_list_outdo.php" name="cutter_out_list_out" method="post">
    <table>
      <tr>
        <th width="10%">申领单号：</th>
        <td width="15%"><?php echo $array['apply_number']; ?></td>
        <th width="10%">申领人：</th>
        <td width="15%"><?php echo $array['employee_name']; ?></td>
        <th width="10%">申领日期：</th>
        <td width="15%"><?php echo $array['apply_date']; ?></td>
        <th width="10%">计划申领日期：</th>
        <td width="15%"><?php echo $array['plan_date']; ?></td>
      </tr>
      <tr>
        <th>类型：</th>
        <td><?php echo $array['type']; ?></td>
        <th>规格：</th>
        <td><?php echo $array['specification']; ?></td>
        <th>材质：</th>
        <td><?php echo $array_cutter_texture[$array['texture']]; ?></td>
        <th>硬度：</th>
        <td><?php echo $array['hardness']; ?></td>
      </tr>
      <tr>
        <th>模具编号：</th>
        <td><?php echo $array['mould_number']; ?></td>
        <th>出库数量：</th>
        <td><?php echo $array['quantity']; ?> 件</td>
        <th>更换数量：</th>
        <td><input type="text" name="old_quantity" id="old_quantity" value="<?php echo $array['old_quantity']; ?>" class="input_txt" />
          件</td>
        <th>出库日期：</th>
        <td><input type="text" name="dodate" value="<?php echo $array['dodate']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>备注：</th>
        <td colspan="7"><input type="text" name="remark" value="<?php echo $array['remark']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td colspan="7"><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="inoutid" value="<?php echo $inoutid; ?>" />
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
	$sql_order_list = "SELECT `db_cutter_order_list`.`listid`,`db_cutter_order_list`.`surplus`,`db_cutter_order`.`order_number`,`db_cutter_order`.`order_date` FROM `db_cutter_order_list` INNER JOIN `db_cutter_order` ON `db_cutter_order`.`orderid` = `db_cutter_order_list`.`orderid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` WHERE `db_cutter_order_list`.`surplus` >0 AND `db_cutter_purchase_list`.`cutterid` = '$cutterid' ORDER BY `db_cutter_order`.`order_date` ASC,`db_cutter_order_list`.`listid` ASC";
	$result_order_list = $db->query($sql_order_list);
?>
<div id="table_list">
  <?php if($result_order_list->num_rows){ ?>
  <form action="cutter_out_list_outdo.php" name="cutter_out_list_out" method="post">
    <table>
      <caption>
      可出库订单明细
      </caption>
      <tr>
        <th width="14%">合同号</th>
        <th width="14%">订单日期</th>
        <th width="10%">结余</th>
        <th width="10%">出库数量</th>
        <th width="10%">更换数量</th>
        <th width="8%">单位</th>
        <th width="14%">出库日期</th>
        <th width="20%">备注</th>
      </tr>
      <?php
      while($row_order_list = $result_order_list->fetch_assoc()){
		  $listid = $row_order_list['listid'];
		  $surplus = $row_order_list['surplus'];
		  if($total_out_quantity >= $surplus){
			  $plan_quantity = $surplus;
			  $total_out_quantity = $total_out_quantity - $surplus;
		  }else{
			  $plan_quantity = $total_out_quantity;
		  }
	  ?>
      <tr>
        <td><?php echo $row_order_list['order_number']; ?><input type="hidden" name="listid[]" value="<?php echo $listid; ?>" /></td>
        <td><?php echo $row_order_list['order_date']; ?></td>
        <td><?php echo $row_order_list['surplus']; ?></td>
        <td><input type="text" name="quantity[]" id="quantity-<?php echo $listid; ?>" value="<?php echo $plan_quantity; ?>" class="input_txt" size="10" /></td>
        <td><input type="text" name="old_quantity[]" id="old_quantity-<?php echo $listid; ?>" value="<?php echo $plan_quantity; ?>" class="input_txt" size="10" /></td>
        <td>件</td>
        <td><input type="text" name="dodate[]" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" class="input_txt" /></td>
        <td><input type="text" name="remark[]" value="<?php echo $array['remark']; ?>" class="input_txt" size="30" />
        </td>
      </tr>
      <?php } ?>
      <tr>
        <td colspan="8"><input type="submit" name="submit" id="submit" value="确认" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="apply_listid"value="<?php echo $apply_listid; ?>" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php } ?>
</div>
<?php } ?>
<?php include "../footer.php"; ?>
</body>
</html>