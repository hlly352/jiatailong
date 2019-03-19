<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
if($_SESSION['login_status'] == true){
	header("location:/myjtl/");
}
if($_POST['submit']){
	$account = trim($_POST['account']);
	$sql = "SELECT `password`,`email` FROM `db_employee` WHERE `account` = '$account' AND `account_status` = 1";
	$result = $db->query($sql);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/base.css" type="text/css" rel="stylesheet" />
<link href="../css/login.css" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<title>账号登录-苏州嘉泰隆</title>
</head>

<body>
<div id="login_header">
  <h3 style="padding-left:100px; font-size:28px; font-weight:bold;">苏州嘉泰隆实业有限公司</h3>
</div>
<div id="login">
  <?php
  if($result->num_rows){
      $array = $result->fetch_assoc();
      $employee_name = $array['employee_name'];
      $password = $array['password'];
      $email_name = $array['email'];
      $md5str = md5($account.'+'.$password);
      $basestr = base64_encode($account.'.'.$md5str);
      $email_subject = "内网账号密码找回";
      $email_content = "您好，".$account."<br />"."感谢您使用账号密码找回功能，如您确定此密码找回功能是您发起，请点击下面的链接地址，按流程重新设置您的账号新密码。如有疑问请联系管理员<br /><a href=\"http://10.202.3.252/passport/reset_password.php?p=".$basestr."\" target=\"_blank\">点击找回密码</a><br />链接地址:http://10.202.3.252/passport/reset_password.php?p=".$basestr."</p>";
      $dotime = fun_gettime();
      $sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
      $db->query($sql_email);
  ?>
  <p style="width:800px; height:400px; line-height:400px; margin:0 auto; text-align:center; background:url(../images/yes.png) no-repeat left center;">您好，账号<?php echo $account ?>密码重置链接已成功发送到您的邮件，请查收邮件并重新设置账号密码。</p>
  <?php }else{ ?>
  <p style="width:800px; height:400px; line-height:400px; margin:0 auto; text-align:center; background:url(../images/no.png) no-repeat left center;">您好，账号<?php echo $account ?>密码重置失败，账号不存在或者被关闭，如有疑问请与管理员联系。</p>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>