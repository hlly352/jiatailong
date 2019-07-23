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
<script type="text/javascript">
$(function(){
  $('input[name=submit]').live('click',function(){
  	//遍历所有的发票号是否填写
  	var num = $('.invoice_no').size();
  	var amounts = 0;
  	for(var i=0;i<num;i++){
  		var invoice_no = $.trim($('.invoice_no').eq(i).val());
  		var invoice_amount = parseFloat($.trim($('.invoice_amount').eq(i).val()));
  		 amounts += invoice_amount;
    	if(!invoice_no){
     	  alert('请输入发票号');
    	  $(".invoice_no").eq(i).focus();
    	  return false;
   		 }
  		if(!invoice_amount){
  			alert('请填写发票金额');
  			$('.invoice_amount').eq(i).focus();
  			return false;
  		}
  	    if(!rf_a.test(invoice_amount)){
      		alert('请输入数字');
            $('.invoice_amount').eq(i).focus();
      		return false;
    	}
  		}
  	//发票总金额必须与对账金额相等
  	var account_amount = parseFloat($.trim($('#account_amount').val()));
  	if(account_amount != amounts){
  		alert('发票总金额必须与对账金额相等');
  		$('.invoice_amount:last').focus();
  		return false;
  		}
    })
  //点击添加按钮
 $("#add_invoice").live('click',function(){
 	var new_invoice = '<tr>        <th width="12%">发票号：</th>        <td width="20%">          <input type="text" id="invoice_no"  class="invoice_no" name="invoice_no[]">        </td>        <th width="12%">发票金额：</th>        <td>          <input type="text"  id="invoice_amount" class="invoice_amount" name="invoice_amount[]" value="<?php echo $row['amount'] ?>" >        </td>         <th width="12%">开票日期：</th>        <td>          <input type="text" value="<?php echo date("Y-m-d") ?>" onfocus="WdatePicker({dateFmt:\'yyyy-MM-dd\',isShowClear:false,readOnly:true})" name="invoice_date[]" >        </td>      </tr>';
 	$(this).parent().parent().before(new_invoice);
 	if($('.invoice_no').size() == 2){
 		var but = '&nbsp;<input type="button" id="cancel" class="button" value="撤销">';
 		$(this).after(but);
 	}else if($('.invoice_no').size() == 1){
 		$('#cancel').remove();
 	}
 })
 //点击撤销按钮
 $('#cancel').live('click',function(){
 	$(this).parent().parent().prev().remove();
 	if($('.invoice_no').size() == 1){
 		$(this).remove();
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
  if($action == "add"){
  //查找当前供应商的信息
  $sql = "SELECT `db_material_account`.`accountid`,`db_supplier`.`supplier_cname`,`db_material_account`.`account_time`,`db_material_account`.`amount` FROM `db_material_account` INNER JOIN `db_supplier` ON `db_material_account`.`supplierid` = `db_supplier`.`supplierid` WHERE `accountid` =".$accountid;
  $result = $db->query($sql);
  if($result->num_rows){
    $row = $result->fetch_assoc();
    //通过对账单号查询对应的合同号
    $order_sql = "SELECT `db_material_order`.`order_number` FROM `db_material_account_list` INNER JOIN `db_material_inout` ON `db_material_account_list`.`inoutid` = `db_material_inout`.`inoutid` INNER JOIN `db_material_order_list` ON `db_material_inout`.`listid` = `db_material_order_list`.`listid` INNER JOIN `db_material_order` ON `db_material_order_list`.`orderid` = `db_material_order`.`orderid` WHERE `accountid`='".$row['accountid']."' GROUP BY `db_material_order`.`order_number`";
    $result_order = $db->query($order_sql);
    
  ?>
  <h4>发票信息录入</h4>
  <form action="material_invoice_info_do.php?action=add" name="employee_goout" method="post">
    <table>
    <tr>
        <th width="12">合同号：</th>
        <td colspan="5">
          <?php
          if($result_order->num_rows){
          $i = 1;
      while($row_order = $result_order->fetch_assoc()){
        echo ' PO:'.$row_order['order_number'];
        echo $i%10 == 0?"<br />":"";
       $i++;
        
      }
      }
      ?>
        </td>
      </tr>
      <tr>
        <th width="12%">供应商：</th>
        <td width="20%">
          <input type="text" readonly value="<?php echo $row['supplier_cname'] ?>">
        </td>
         <th width="12%">对账金额：</th>
        <td>
          <input type="text" readonly id="account_amount" value="<?php echo $row['amount'] ?>" >
        </td>
        <th width="12%">对账日期：</th>
        <td>
          <input type="text" readonly  value="<?php echo $row['account_time'] ?>" >
        </td>
      </tr> 
      <tr>
        <th width="12%">发票号：</th>
        <td width="20%">
          <input type="text" id="invoice_no"  class="invoice_no" name="invoice_no[]">
        </td>
        <th width="12%">发票金额：</th>
        <td>
          <input type="text"  id="invoice_amount" class="invoice_amount" name="invoice_amount[]" value="<?php echo $row['amount'] ?>" >
        </td>
         <th width="12%">开票日期：</th>
        <td>
          <input type="text" value="<?php echo date('Y-m-d') ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" name="invoice_date[]" >
        </td>
      </tr>
      <tr>
        <td colspan="6" style="text-align:center">
          <input type="hidden" name="accountid" value="<?php echo $row['accountid'] ?>">
          <input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="" class="button" id="add_invoice" value="添加">
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