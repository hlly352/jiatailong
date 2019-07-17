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
	

  ?>
  <h4>发票信息录入</h4>
  <form action="material_invoice_info_do.php?action=add" name="employee_goout" method="post">
    <table>
      <tr>
        <th width="12%">供应商：</th>
        <td width="20%">
        	<input type="text" readonly value="<?php echo $row['supplier_cname'] ?>">
        </td>
         <th width="12%">对账金额：</th>
        <td>
        	<input type="text" readonly value="<?php echo $row['amount'] ?>" >
        </td>
        <th width="12%">对账日期：</th>
        <td>
        	<input type="text" readonly value="<?php echo $row['account_time'] ?>" >
        </td>
      </tr> 
      <tr>
        <th width="12%">发票号：</th>
        <td width="20%">
        	<input type="text" name="invoice_no">
        </td>
        <th width="12%">发票金额：</th>
        <td>
        	<input type="text" name="invoice_amount" value="<?php echo $row['amount'] ?>" >
        </td>
         <th width="12%">开票日期：</th>
        <td>
        	<input type="text" value="<?php echo date('Y-m-d') ?>" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,readOnly:true})" name="invoice_date" >
        </td>
      </tr>
      <tr>
        <td colspan="6" style="text-align:center">
        	<input type="hidden" name="accountid" value="<?php echo $row['accountid'] ?>">
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