<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
if($_SESSION['login_status'] == true){
	header("location:/myjtl/");
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/base.css" type="text/css" rel="stylesheet" />
<link href="../css/login.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<script language="javascript" type="text/javascript" src="../js/jquery-1.6.4.min.js"></script>
<script language="javascript" type="text/javascript">
$(function(){
	$(".login_input_txt:input").focus(function(){
		$(this).addClass("focus");
	}).blur(function(){
		$(this).removeClass("focus");
	})
	$("#submit").click(function(){
		var account = $("#account").val();
		if(!$.trim(account)){
			$("#account").focus();
			return false;
		}
		var account_status = $("#account_status").val();
		if(account_status == 0){
			$("#account_status").focus();
			return false;
		}
	})
	$("#account").blur(function(){
		var account = $(this).val();
		if($.trim(account)){
			$.post("../ajax_function/employee_account_check.php",{
				   account:account
			},function(data,textStatus){
				var array_check_result = data.split('#');
				$("#account_tag").html(array_check_result[0]);
				$("#account_status").val(array_check_result[1]);
			})
		}else{
			$("#account_tag").html('');
			$("#account_status").val('')
		}
	})
})
</script>
<title>账号登录-苏州嘉泰隆</title>
</head>

<body>
<div id="login_header">
  <h3 style="padding-left:100px; font-size:28px; font-weight:bold;">苏州嘉泰隆实业有限公司</h3>
</div>
<div id="login">
  <div id="login_left">
    <form action="get_passworddo.php" name="get_password" method="post">
      <table>
        <caption>
        账号密码找回
        </caption>
        <tr>
          <th>账号：</th>
          <td><input type="text" name="account" id="account" class="login_input_txt" size="30" />
            <span id="account_tag" class="tag"></span></td>
        </tr>
        <tr>
          <th>&nbsp;</th>
          <td><input type="submit" name="submit" id="submit" value="找回密码" class="login_button" />
          <input type="button" name="button" value="返回登录" class="login_button" onclick="location.href='login.php'" />
          <input type="hidden" name="account_status" id="account_status" /></td>
        </tr>
      </table>
    </form>
  </div>
  <div id="login_right">
    <dl>
      <dt>密码找回说明:</dt>
      <dd>1. 输入账号，点击找回密码，系统会自动发送重置密码邮件至您账号关联邮箱；</dd>
      <dd>2. 请确认您要找回密码的账号为有效状态；</dd>
      <dd>3. 如有其他疑问请与管理员联系；</dd>
    </dl>
  </div>
  <div class="clear"></div>
</div>
<?php include "../footer.php"; ?>
</body>
</html>