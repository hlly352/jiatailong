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
		var employee_name = $("#employee_name").val();
		if(!$.trim(employee_name)){
			$("#employee_name").focus();
			return false;
		}
		var password = $("#password").val();
		if(password.length < 6){
			$("#password").focus();
			return false;
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
  <?php
  $str = trim($_GET['p']);
  $base_str = base64_decode($str);
  $array_str = explode('.',$base_str);
  $account = $array_str[0];
  $md5_str = $array_str[1];
  $sql = "SELECT `password` FROM `db_employee` WHERE `account` = '$account' AND `account_status` = 1";
  $result = $db->query($sql);
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $password = $array['password'];
	  $check_code = md5($account.'+'.$password);
	  if($check_code == $md5_str){
  ?>
  <div id="login_left">
    <form action="reset_passworddo.php" name="reset_password" method="post">
      <table>
        <caption>
        账号密码重置
        </caption>
        <tr>
          <th>账号：</th>
          <td><input type="text" name="account" value="<?php echo $account; ?>" class="login_input_txt" size="30" readonly="readonly" /></td>
        </tr>
        <tr>
          <th>员工：</th>
          <td><input type="text" name="employee_name" id="employee_name" class="login_input_txt" size="30" /></td>
        </tr>
        <tr>
          <th>新密码：</th>
          <td><input type="password" name="password" id="password" class="login_input_txt" size="30" /></td>
        </tr>
        <tr>
          <th>&nbsp;</th>
          <td><input type="submit" name="submit" id="submit" value="密码重置" class="login_button" />
            <input type="button" name="button" value="返回登录" class="login_button" onclick="location.href='login.php'" /></td>
        </tr>
        <tr>
          <th>&nbsp;</th>
          <td><span class="tag"><?php if($_GET['do_status'] == 'F') echo "密码重置失败，请重新输入"; ?></span></td>
        </tr>
      </table>
    </form>
  </div>
  <div id="login_right">
    <dl>
      <dt>密码重置说明:</dt>
      <dd>1. 输入员工姓名需与账号匹配；</dd>
      <dd>2. 密码长度至少6位；</dd>
      <dd>3. 密码重置成功后自动跳转到登录页；</dd>
      <dd>4. 密码重置异常请与管理员联系；</dd>
    </dl>
  </div>
  <div class="clear"></div>
  <?php
	  }
  }
  ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>