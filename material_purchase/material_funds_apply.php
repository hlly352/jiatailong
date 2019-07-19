  <?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$action = fun_check_action();
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
<script type="text/javascript">
$(function(){
	$('input[name=submit]').live('click',function(){
    var invoice_amount = $('#invoice_amount').val();
    var invoice_no = $("#invoice_no").val();
    var apply_amount = $("#apply_amount").val();
    if(!invoice_no){
      alert('请输入发票号');
      $("#invoice_no").focus();
      return false;
    }
    if(!invoice_amount){
      alert('请填写发票金额');
      $('#invoice_amount').focus();
      return false;
    }
		if(!rf_a.test(invoice_amount)){
			alert('请输入数字');
			$('#invoice_amount').focus();
			return false;
		}
    if(!apply_amount){
      alert('请输入付款金额');
      $('#apply_amount').focus();
      return false;
    }
    if(!rf_a.test(apply_amount)){  
      alert('请输入数字');
      $('#apply_amount').focus();
      return false;
    }
	})
})
</script>
<title>发票管理-希尔林</title>
</head>
<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  $accountid = $_GET['id'];
  if($action == "apply"){
	//查找当前供应商的信息
	$sql = "SELECT `db_material_account`.`supplierid`,`db_material_invoice_list`.`date`,`db_material_account`.`apply_amount`,`db_material_invoice_list`.`invoice_no`,`db_material_account`.`accountid`,`db_supplier`.`supplier_cname`,`db_material_account`.`account_time`,`db_material_account`.`amount` FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` INNER JOIN `db_material_invoice_list` ON `db_material_invoice_list`.`accountid` = `db_material_account`.`accountid` WHERE `db_material_account`.`accountid` =".$accountid;

	$result = $db->query($sql);
	if($result->num_rows){
		$row = $result->fetch_assoc();
	

  ?>
  <h4>付款申请</h4>
  <form action="material_funds_manage_do.php?action=approval" name="employee_goout" method="post">
    <table>
      <tr>
        <th width="12%">供应商：</th>
        <td width="20%">
        	<input type="text" readonly value="<?php echo $row['supplier_cname'] ?>">
        </td>
         <th width="12%">对账金额：</th>
        <td>
        	<input type="text" name="amount" readonly value="<?php echo $row['amount'] ?>" >
        </td>
        <th width="12%">对账日期：</th>
        <td>
        	<input type="text" readonly value="<?php echo $row['account_time'] ?>" >
        </td>
      </tr> 
      <tr>
        <th width="12%">发票号：</th>
        <td width="20%">
        	<input type="text" id="invoice_no" name="invoice_no" value="<?php echo $row['invoice_no'] ?>">
        </td>
        <th width="12%">发票金额：</th>
        <td>
        	<input type="text"  id="invoice_amount" name="invoice_amount" value="<?php echo $row['amount'] ?>" >
        </td>
         <th width="12%">开票日期：</th>
        <td>
        	<input type="text" readonly value="<?php echo $row['date'] ?>">
        </td>
      </tr>
      <tr>
         <th width="12%">未款金额：</th>
        <td width="20%">
          <input type="text" readonly value="<?php echo number_format(($row['amount'] - $row['apply_amount']),2,'.','')?>">
        </td>
         <th width="12%">付款金额：</th>
        <td width="20%">
          <input type="text" id="apply_amount" name="actual_amount" value="<?php echo number_format(($row['amount'] - $row['apply_amount']),2,'.','')?>">
        </td>
        <th width="12%">备注：</th>
        <td>
          <input type="text"   name="remark">
        </td>
      </tr>
      <tr>
        <td colspan="6" style="text-align:center">
        	<input type="hidden" name="accountid" value="<?php echo $row['accountid'] ?>">
          <input type="hidden" name="supplierid" value="<?php echo $row['supplierid'] ?>">
          <input type="hidden" name="apply_amount" value="<?php echo $row['apply_amount'] ?>">
        	<input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="employeeid" value="<?php echo $employeeid; ?>" />
          <input type="hidden" name="action" value="<?php echo $action; ?>" /></td>
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