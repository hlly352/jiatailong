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
<link href="../css/style.css" type="text/css" rel="stylesheet"/>
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script langeage="javascript" type="text/javascript" src="../js/add_customer.js"></script>
<script language="javascript" type="text/javascript">


</script>
<title>模具报价-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($action == 'add'){
	  $sql_employee = "SELECT `employee_name`,`phone`,`email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
	  $result_employee = $db->query($sql_employee);
	  $array_employee = $result_employee->fetch_assoc();
  ?>
  <h4>添加客户</h4>
  
 
  <form action="customer_datado.php" name="customer_datado" method="post" enctype="multipart/form-data">
  <div class="reg_div">
    <p>客户信息</p>
    <ul class="reg_ul">
      <li>
          <span>客户名称：</span>
          <input type="text" name="customer_name" value="" placeholder="4-8位用户名" class="reg_user">
          <span class="tip user_hint"></span>
      </li>
      <li>
          <span>客户代码：</span>
          <input type="text" name="customer_code" value="" placeholder="4-8位用户名" class="reg_user">
          <span class="tip user_hint"></span>
      </li>
      <li>
          <span>联系人：</span>
          <input type="text" name="" value=""  placeholder="联系人姓名" class="reg_contacts">
          <span class="tip contacts_hint"></span>
      </li>
        <li>
          <span>手机号码：</span>
          <input type="email" name="" value="" placeholder="手机号" class="reg_mobile">
          <span class="tip mobile_hint"></span>
      </li>
        <li>
          <span>邮箱：</span>
          <input type="text" name="" value="" placeholder="邮箱" class="reg_email">
          <span class="tip email_hint"></span>
      </li>
      <li>
          <span>地址：</span>
          <input type="password" name="" value="" placeholder="确认密码" class="reg_confirm">
          <span class="tip confirm_hint"></span>
      </li>
    
    
      <li>
        <button type="submit" name="button" class="red_button">添加</button>
      </li>
    </ul>
  </div>
 </form>
  <?php
  	}  
  
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>