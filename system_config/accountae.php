<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = fun_check_int($_GET['id']);
$sql = "SELECT `db_employee`.`employee_name`,`db_employee`.`account`,`db_employee`.`email`,`db_employee`.`employee_status`,`db_employee`.`account_status`,`db_department`.`dept_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `employeeid` = '$employeeid'";
$result = $db->query($sql);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/system_base.css" type="text/css" rel="stylesheet" />
<link href="css/main.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/main.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var account = $("#account").val();
		if(!$.trim(account)){
			$("#account").focus();
			return false;
		}
		var tag = $("#tag").html();
		if(tag){
			$("#account").focus();
			return false;
		}
		/*
		var email = $("#email").val();
		if(!email_reg.test(email)){
			$("#email").focus();
			return false;
		}
		*/
	})
	$("#email").blur(function(){
		var email = $(this).val();
		if(email_reg.test(email)){
			email = email.toLowerCase();
			$("#email").val(email);
		}
	})
	$("#account").blur(function(){
		var account = $(this).val();
		var employeeid = $("#employeeid").val();
		if($.trim(account)){
			$.post('../ajax_function/account_check.php',{
				account:account,
				employeeid:employeeid
			},function(data,textstatus){
				$("#tag").html(data);
			})
		}else{
			$("#tag").html('');
		}
	})
})
</script>
<title>系统配置-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $account_status = $array['account_status'];
  ?>
  <h4>员工账号修改</h4>
  <form action="accountdo.php" name="employee_account" method="post">
    <table>
      <tr>
        <th width="20%">员工：</th>
        <td width="80%"><?php echo $array['employee_name']; ?></td>
      </tr>
      <tr>
        <th>部门：</th>
        <td><?php echo $array['dept_name']; ?></td>
      </tr>
      <tr>
        <th>员工状态：</th>
        <td><?php echo $array_employee_status[$array['employee_status']]; ?></td>
      </tr>
      <tr>
        <th>账号状态：</th>
        <td><select name="account_status">
            <?php foreach($array_status as $status_key=>$status_value){ ?>
            <option value="<?php echo $status_key; ?>"<?php if($status_key == $account_status) echo " selected=\"selected\""; ?>><?php echo $status_value; ?></option>
            <?php } ?>
          </select>
          <span class="tag"> *账号设置无效，将关闭该账号所有程序访问权限。</span></td>
      </tr>
      <tr>
        <th>账号：</th>
        <td><input type="text" name="account" id="account" value="<?php echo $array['account']; ?>" class="input_txt" />
          <span class="tag" id="tag"></span></td>
      </tr>
      <tr>
        <th>邮箱：</th>
        <td><input type="text" name="email" id="email" value="<?php echo $array['email']; ?>" size="30" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="submit" name="submit_password" value="密码重置" class="button"<?php if(!$account_status) echo " disabled=\"disabled\""; ?> />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="employeeid" id="employeeid" value="<?php echo $employeeid; ?>" />
      </tr>
    </table>
  </form>
  <?php
  }else{
	  echo "<p class=\"tag\">系统提示：暂无记录！</p>";
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>