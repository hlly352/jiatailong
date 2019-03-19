<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action($_GET['action']);
$employeeid = $_SESSION['employee_info']['employeeid'];
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
	$("#pay_amount").focus();
	$("#submit").click(function(){
		var pay_amount = $("#pay_amount").val();
		if(!rf_b.test(pay_amount)){
			$("#pay_amount").focus();
			return false;
		}
	})
	$("#pay_amount").blur(function(){
		var deafult_pay_amount = this.defaultValue;
		var pay_amount = $(this).val();
		if(rf_b.test(pay_amount)){
			var linkid = $("#linkid").val();
			var payid = $("#payid").val();
			var data_type = $("#data_type").val();
			var action = $("#action").val();
			$.post("../ajax_function/pay_amount_check.php",{
				   pay_amount:pay_amount,
				   linkid:linkid,
				   payid:payid,
				   data_type:data_type,
				   action:action
			},function(data,textStatus){
				if(data == 0){
					alert('付款金额超出可最大金额');
					$("#pay_amount").val(deafult_pay_amount);
				}else if(data == 1){
					$("#pay_amount").val(parseFloat(pay_amount).toFixed(2));
				}
			})
		}
	})
})
</script>
<title>财务管理-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<?php
if($action == "add"){
	$outwardid = fun_check_int($_GET['id']);
	$sql = "SELECT `db_mould_outward`.`mouldid`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`cost`,`db_mould_outward`.`iscash`,`db_mould_outward`.`applyer`,`db_mould_outward`.`plan_date`,`db_mould_outward`.`actual_date`,`db_mould_outward`.`inout_status`,`db_mould_outward`.`outward_status`,`db_mould_outward`.`remark`,`db_mould_outward`.`dotime`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_mould_outward_type`.`outward_typename`,`db_employee`.`employee_name` FROM `db_mould_outward` LEFT JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_outward`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_outward`.`workteamid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_mould_outward`.`outward_typeid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_outward`.`employeeid` WHERE `db_mould_outward`.`outwardid` = '$outwardid' AND `db_mould_outward`.`iscash` = 1 AND `db_mould_outward`.`outward_status` = 1";
	$result = $db->query($sql);
	if($result->num_rows){
		$array = $result->fetch_assoc();
		$mould_number = $array['mouldid']?$array['mould_number']:'--';
		$inout_status = $array['inout_status'];
		$actual_date = $inout_status?$array['actual_date']:'--';
		$total_amount = $array['cost'];
		$order_date = $array['order_date'];
		$sql_pay_amount = "SELECT SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` = '$outwardid' AND `data_type` = 'MO' GROUP BY `linkid`";
		$result_pay_amount = $db->query($sql_pay_amount);
		if($result_pay_amount->num_rows){
			$array_pay_amount = $result_pay_amount->fetch_assoc();
			$total_pay_amount = $array_pay_amount['total_pay_amount'];
		}else{
			$total_pay_amount = 0;
		}
		$wait_pay_amount = number_format(($total_amount - $total_pay_amount),2,'.','');
?>
<div id="table_sheet">
  <h4>付款加工信息</h4>
  <table>
    <tr>
      <th width="10%">模具编号：</th>
      <td width="15%"><?php echo $mould_number; ?></td>
      <th width="10%">零件编号：</th>
      <td width="15%"><?php echo $array['part_number']; ?></td>
      <th width="10%">外协时间：</th>
      <td width="15%"><?php echo $array['order_date']; ?></td>
      <th width="10%">申请组别：</th>
      <td width="15%"><?php echo $array['workteam_name']; ?></td>
    </tr>
    <tr>
      <th>外协单号：</th>
      <td><?php echo $array['order_number']; ?></td>
      <th>数量：</th>
      <td><?php echo $array['quantity']; ?></td>
      <th>供应商：</th>
      <td><?php echo $array['supplier_cname']; ?></td>
      <th>类型：</th>
      <td><?php echo $array['outward_typename']; ?></td>
    </tr>
    <tr>
      <th>金额：</th>
      <td><?php echo $array['cost']; ?></td>
      <th>申请人：</th>
      <td><?php echo $array['applyer']; ?></td>
      <th>计划回厂：</th>
      <td><?php echo $array['plan_date']; ?></td>
      <th>实际回厂：</th>
      <td><?php echo $actual_date; ?></td>
    </tr>
    <tr>
      <th>现金：</th>
      <td><?php echo $array_is_status[$array['iscash']]; ?></td>
      <th>进度状态：</th>
      <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
      <th>状态：</th>
      <td><?php echo $array_status[$array['outward_status']]; ?></td>
      <th>操作人：</th>
      <td><?php echo $array['employee_name']; ?></td>
    </tr>
    <tr>
      <th>操作时间：</th>
      <td><?php echo $array['dotime']; ?></td>
      <th>备注：</th>
      <td colspan="5"><?php echo $array['remark']; ?></td>
    </tr>
  </table>
</div>
<div id="table_sheet">
  <h4>付款信息添加</h4>
  <form action="pay_listdo.php" name="pay_list" method="post">
    <table>
      <tr>
        <th width="20%">付款日期：</th>
        <td width="80%"><input type="text" name="pay_date" value="<?php echo date('Y-m-d'); ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true,minDate:'<?php echo $order_date; ?>'})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>付款金额：</th>
        <td><input type="text" name="pay_amount" id="pay_amount" value="<?php echo $wait_pay_amount; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>备注：</th>
        <td><input type="text" name="remark" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="linkid" id="linkid" value="<?php echo $outwardid; ?>" />
          <input type="hidden" name="data_type" id="data_type" value="MO" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
	}
}elseif($action == "edit"){
	$payid = fun_check_int($_GET['id']);
	$sql = "SELECT `db_cash_pay`.`pay_date`,`db_cash_pay`.`pay_amount`,`db_cash_pay`.`remark` AS `pay_remark`,`db_mould_outward`.`outwardid`,`db_mould_outward`.`mouldid`,`db_mould_outward`.`part_number`,`db_mould_outward`.`order_date`,`db_mould_outward`.`order_number`,`db_mould_outward`.`quantity`,`db_mould_outward`.`cost`,`db_mould_outward`.`iscash`,`db_mould_outward`.`applyer`,`db_mould_outward`.`plan_date`,`db_mould_outward`.`actual_date`,`db_mould_outward`.`inout_status`,`db_mould_outward`.`outward_status`,`db_mould_outward`.`remark` AS `outward_remark`,`db_mould_outward`.`dotime`,`db_mould`.`mould_number`,`db_supplier`.`supplier_cname`,`db_mould_workteam`.`workteam_name`,`db_mould_outward_type`.`outward_typename`,`db_employee`.`employee_name` FROM `db_cash_pay` INNER JOIN `db_mould_outward` ON `db_mould_outward`.`outwardid` = `db_cash_pay`.`linkid` LEFT JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_outward`.`mouldid` INNER JOIN `db_supplier` ON `db_supplier`.`supplierid` = `db_mould_outward`.`supplierid` INNER JOIN `db_mould_workteam` ON `db_mould_workteam`.`workteamid` = `db_mould_outward`.`workteamid` INNER JOIN `db_mould_outward_type` ON `db_mould_outward_type`.`outward_typeid` = `db_mould_outward`.`outward_typeid` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_outward`.`employeeid` WHERE `db_cash_pay`.`payid` = '$payid' AND `db_cash_pay`.`data_type` = 'MO' AND `db_cash_pay`.`employeeid` = '$employeeid'";
	$result = $db->query($sql);
	if($result->num_rows){
		$array = $result->fetch_assoc();
		$outwardid = $array['outwardid'];
		$mould_number = $array['mouldid']?$array['mould_number']:'--';
		$inout_status = $array['inout_status'];
		$actual_date = $inout_status?$array['actual_date']:'--';
		$total_amount = $array['cost'];
		$order_date = $array['order_date'];
		$sql_pay_amount = "SELECT SUM(`pay_amount`) AS `total_pay_amount` FROM `db_cash_pay` WHERE `linkid` = '$outwardid' AND `data_type` = 'MO' GROUP BY `linkid`";
		$result_pay_amount = $db->query($sql_pay_amount);
		if($result_pay_amount->num_rows){
			$array_pay_amount = $result_pay_amount->fetch_assoc();
			$total_pay_amount = $array_pay_amount['total_pay_amount'];
		}else{
			$total_pay_amount = 0;
		}
		$wait_pay_amount = number_format(($total_amount - $total_pay_amount),2);
?>
<div id="table_sheet">
  <h4>付款加工信息</h4>
  <table>
    <tr>
      <th width="10%">模具编号：</th>
      <td width="15%"><?php echo $mould_number; ?></td>
      <th width="10%">零件编号：</th>
      <td width="15%"><?php echo $array['part_number']; ?></td>
      <th width="10%">外协时间：</th>
      <td width="15%"><?php echo $array['order_date']; ?></td>
      <th width="10%">申请组别：</th>
      <td width="15%"><?php echo $array['workteam_name']; ?></td>
    </tr>
    <tr>
      <th>外协单号：</th>
      <td><?php echo $array['order_number']; ?></td>
      <th>数量：</th>
      <td><?php echo $array['quantity']; ?></td>
      <th>供应商：</th>
      <td><?php echo $array['supplier_cname']; ?></td>
      <th>类型：</th>
      <td><?php echo $array['outward_typename']; ?></td>
    </tr>
    <tr>
      <th>金额：</th>
      <td><?php echo $array['cost']; ?></td>
      <th>申请人：</th>
      <td><?php echo $array['applyer']; ?></td>
      <th>计划回厂：</th>
      <td><?php echo $array['plan_date']; ?></td>
      <th>实际回厂：</th>
      <td><?php echo $actual_date; ?></td>
    </tr>
    <tr>
      <th>现金：</th>
      <td><?php echo $array_is_status[$array['iscash']]; ?></td>
      <th>进度状态：</th>
      <td><?php echo $array_mould_inout_status[$inout_status]; ?></td>
      <th>状态：</th>
      <td><?php echo $array_status[$array['outward_status']]; ?></td>
      <th>操作人：</th>
      <td><?php echo $array['employee_name']; ?></td>
    </tr>
    <tr>
      <th>操作时间：</th>
      <td><?php echo $array['dotime']; ?></td>
      <th>备注：</th>
      <td colspan="5"><?php echo $array['outward_remark']; ?></td>
    </tr>
  </table>
</div>
<div id="table_sheet">
  <h4>付款信息修改</h4>
  <form action="pay_listdo.php" name="pay_list" method="post">
    <table>
      <tr>
        <th width="20%">付款日期：</th>
        <td width="80%"><input type="text" name="pay_date" value="<?php echo $array['pay_date']; ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true,minDate:'<?php echo $order_date; ?>'})" class="input_txt" /></td>
      </tr>
      <tr>
        <th>付款金额：</th>
        <td><input type="text" name="pay_amount" id="pay_amount" value="<?php echo $array['pay_amount']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>备注：</th>
        <td><input type="text" name="remark" value="<?php echo $array['pay_remark'] ?>" class="input_txt" size="35" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="button" name="button" value="添加付款" class="button" onclick="location.href='?id=<?php echo $outwardid; ?>&action=add'" />
          <input type="hidden" name="linkid" id="linkid" value="<?php echo $outwardid; ?>" />
          <input type="hidden" name="payid" id="payid" value="<?php echo $payid; ?>" />
          <input type="hidden" name="data_type" id="data_type" value="MO" />
          <input type="hidden" name="action" id="action" value="<?php echo $action; ?>" /></td>
      </tr>
    </table>
  </form>
</div>
<?php
	}
}
?>
<?php
$sql_pay = "SELECT `db_cash_pay`.`payid`,`db_cash_pay`.`pay_date`,`db_cash_pay`.`pay_amount`,`db_cash_pay`.`employeeid`,`db_cash_pay`.`dotime`,`db_cash_pay`.`remark`,`db_employee`.`employee_name` FROM `db_cash_pay` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_cash_pay`.`employeeid` WHERE `db_cash_pay`.`linkid` = '$outwardid' AND `data_type` = 'MO' ORDER BY `db_cash_pay`.`pay_date` DESC,`db_cash_pay`.`payid` DESC";
$result_pay = $db->query($sql_pay);
?>
<div id="table_list">
  <?php if($result_pay->num_rows){ ?>
  <table>
    <caption>
    付款记录
    </caption>
    <tr>
      <th width="4%">ID</th>
      <th width="16%">付款日期</th>
      <th width="16%">付款金额</th>
      <th width="16%">付款人</th>
      <th width="20%">操作时间</th>
      <th width="24%">备注</th>
      <th width="4%">Edit</th>
    </tr>
    <?php
    while($row_pay = $result_pay->fetch_assoc()){
		$payid = $row_pay['payid'];
		$pay_amount = $row_pay['pay_amount'];
	?>
    <tr>
      <td><?php echo $payid; ?></td>
      <td><?php echo $row_pay['pay_date']; ?></td>
      <td><?php echo $pay_amount; ?></td>
      <td><?php echo $row_pay['employee_name']; ?></td>
      <td><?php echo $row_pay['dotime']; ?></td>
      <td><?php echo $row_pay['remark']; ?></td>
      <td><?php if($row_pay['employeeid'] == $employeeid){ ?><a href="pay_mould_outward_list.php?id=<?php echo $payid; ?>&action=edit"><img src="../images/system_ico/edit_10_10.png" width="10" height="10" /></a><?php } ?></td>
    </tr>
    <?php
	$all_pay_amount += $pay_amount;
    }
	?>
    <tr>
      <td colspan="2">Total</td>
      <td><?php echo number_format($all_pay_amount,2); ?></td>
      <td colspan="4">&nbsp;</td>
    </tr>
  </table>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无支付记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>