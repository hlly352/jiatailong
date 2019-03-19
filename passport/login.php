<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
if(isset($_SESSION['login_status'])){
	header("location:/myjtl/");
}
if(isset($_POST['submit'])){
	$account = trim($_POST['account']);
	$password = $_POST['password'];
	$sql = "SELECT `employeeid`,`employee_name`,`password`,`account_status` FROM `db_employee` WHERE `account` = '$account'";
	$result = $db->query($sql);
	if($result->num_rows){
		$array = $result->fetch_assoc();
		$employeeid = $array['employeeid'];
		$employee_name = $array['employee_name'];
		$account_status = $array['account_status'];
		if($account_status){
			$account_password = $array['password'];
			$do_password = md5($password.ALL_PW);
			if($do_password == $account_password){
				$login_status = "A";
				$_SESSION['login_status'] = true;
				setcookie('account',$account,time()+365*24*60*60);
				$_SESSION['employee_info'] = array('employeeid'=>$employeeid,'employee_name'=>$employee_name);

				//读取员工权限
				$sql_system = "SELECT `db_system_employee`.`systemid`,`db_system`.`system_dir`,`db_system_employee`.`isadmin`,`db_system_employee`.`isconfirm` FROM `db_system_employee` INNER JOIN `db_system` ON `db_system`.`systemid` = `db_system_employee`.`systemid` WHERE `db_system_employee`.`employeeid` = '$employeeid' AND `db_system`.`system_status` = 1";
				$result_system = $db->query($sql_system);
				if($result_system->num_rows){
					while($row_system = $result_system->fetch_assoc()){
						$array_system[$row_system['systemid']] = $row_system['system_dir'];
						$array_system_shell[$row_system['system_dir']] = array('isadmin'=>$row_system['isadmin'],'isconfirm'=>$row_system['isconfirm']);
					}
					
				}else{
					$array_system = array();
					$array_system_shell = array();
				}
				$_SESSION['system_dir'] = $array_system;
				$_SESSION['system_shell'] = $array_system_shell;
				//print_r($_SESSION['system_dir']);
				//print_r($_SESSION['system_shell']);
				header("location:/myjtl/");
			}else{
				$login_status = "B";
				header("location:?login_status=".$login_status);
			}
		}else{
			$login_status = "C";
			header("location:?login_status=".$login_status);
		}
	}else{
		$login_status = "D";
		header("location:?login_status=".$login_status);
	}
	$dotime = fun_gettime();
	$ip = fun_getip();
	$sql_log = "INSERT INTO `db_login_log` (`logid`,`account`,`employeeid`,`login_status`,`dotime`,`ip`) VALUES (NULL,'$account','$employeeid','$login_status','$dotime','$ip')";
	$db->query($sql_log);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="../css/base.css?v=314" type="text/css" rel="stylesheet" />
<link href="../css/login.css?v=314" type="text/css" rel="stylesheet" />
<link rel="shortcut icon" href="../images/logo/xel.ico" />
<style>
body{
	background-image: url(../images/add/sea.jpg);
	background-size: cover;
}
#login,#login_right{
	background-color: rgba(0,0,0,0.1);
	border: 0px;
}
#footer{
	background-color: rgba(0,0,0,0.1);
	margin-top: 0px;
	border: 0px;
}
</style>

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
		var password = $("#password").val();
		if(!$.trim(password)){
			$("#password").focus();
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
			})
		}else{
			$("#account_tag").html('');
		}
	})
})
</script>
<title>账号登录-苏州希尔林</title>
</head>

<body>
<div id="login_header">
  <!-- <h3>苏州希尔林机械科技有限公司</h3> -->
</div>
<div id="login">
  <div id="login_left">
    <form action="" name="login" method="post">
      <table>
        <caption style="font-size: 22px;">
        账号登录
        </caption>
        <tr>
          <th>账号：</th>
          <td><input type="text" name="account" id="account" value="<?php echo isset($_COOKIE['account'])?$_COOKIE['account']:''; ?>" class="login_input_txt" size="30" />
            <span id="account_tag" class="tag"></span></td>
        </tr>
        <tr>
          <th>密码：</th>
          <td><input type="password" name="password" id="password" class="login_input_txt" size="30" /></td>
        </tr>
        <tr>
          <th>&nbsp;</th>
          <td><input type="submit" name="submit" id="submit" value="登录" class="login_button" />
            <a href="get_password.php">找回密码</a></td>
        </tr>
        <tr>
          <th>&nbsp;</th>
          <td><span class="tag">
            <?php if(isset($_GET['login_status'])){ ?>
            <img src="../images/system_ico/error_10_10.png" width="10" height="10" /> <?php echo $array_login_status[$_GET['login_status']]; ?></span>
            <?php } ?></td>
        </tr>
      </table>
    </form>
  </div>
  <div id="login_right">
    <dl>
      <dt>登录说明:</dt>
      <dd>1. 先登记人事信息后开通账号；</dd>
      <dd>2. 账号获取请咨询人事部门；</dd>
      <dd>3. 登录异常请与人事部门联系；</dd>
    </dl>
  </div>
  <div class="clear"></div>
</div>
<?php include "../footer.php"; ?>
</body>
</html>