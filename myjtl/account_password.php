<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql_employee = "SELECT `employee_name`,`account` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
$result_employee = $db->query($sql_employee);
if($_POST['submit']){
	$employeeid = $_POST['employeeid'];
	$old_password = md5($_POST['old_password'].ALL_PW);
	$sql = "SELECT `password` FROM `db_employee` WHERE `employeeid` = '$employeeid' AND `password` = '$old_password'";
	$result = $db->query($sql);
	if($result->num_rows){
		$password = md5($_POST['password'].ALL_PW);
		$sql = "UPDATE `db_employee` SET `password` = '$password' WHERE `employeeid` = '$employeeid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:?do_status=S");
		}else{
			header("location:?do_status=F");
		}
	}else{
		header("location:?do_status=F");
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/base.css" type="text/css" rel="stylesheet" />
<link href="../css/myjtl.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$("#submit").click(function(){
		var password = $("#password").val();
		if(password.length < 6){
			$("#password").focus();
			return false;
		}
		var again_password = $("#again_password").val();
		if(again_password !== password){
			$("#again_password").focus();
			return false;
		}
	})
	$("#old_password").blur(function(){
		var old_password = $(this).val();
		var employeeid = $("#employeeid").val();
		if(old_password){
			$.post("../ajax_function/account_password_check.php",{
				   password:old_password,
				   employeeid:employeeid
			},function(data,textStatus){
				$("#check_result").html(data);
			})
		}else{
			$("#check_result").html('');
		}
	})
})
</script>
<title>我的希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="myjtl_tag">
  <h4><a href="/myjtl/">MY JTL</a> >> 密码修改</h4>
</div>
<div id="myjtl_table_sheet">
  <?php
  if($result_employee->num_rows){
	  $array_employee = $result_employee->fetch_assoc();
	  $do_status = $_GET['do_status'];
	  if($do_status == 'S'){
		  $do_status_info = "修改密码成功，请重新登录";
	  }elseif($do_status == 'F'){
		  $do_status_info = "修改密码失败，请重新输入新密码";
	  }else{
		  $do_status_info = "";
	  }
  ?>
  <h4>账号密码修改</h4>
  <form action="" name="account_password" method="post">
    <table>
      <tr>
        <th width="30%">员工：</th>
        <td width="70%"><?php echo $array_employee['employee_name']; ?></td>
      </tr>
      <tr>
        <th>账号：</th>
        <td><?php echo $array_employee['account']; ?></td>
      </tr>
      <tr>
        <th>原密码：</th>
        <td><input type="password" name="old_password" id="old_password" class="input_txt" size="25" />
          <span id="check_result" class="tag"></span></td>
      </tr>
      <tr>
        <th>新密码：</th>
        <td><input type="password" name="password" id="password" class="input_txt" size="25" />
          <span class="tag"> *密码长度至少6位</span></td>
      </tr>
      <tr>
        <th>再次输入新密码：</th>
        <td><input type="password" name="again_password" id="again_password" class="input_txt" size="25" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location.href='/myjtl/'" />
          <input type="hidden" name="employeeid" id="employeeid" value="<?php echo $employeeid; ?>" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><span class="tag"><?php echo $do_status_info; ?></span></td>
      </tr>
    </table>
  </form>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>