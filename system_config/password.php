<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = fun_check_int($_GET['id']);
$sql = "SELECT `db_employee`.`employee_name`,`db_employee`.`account`,`db_department`.`dept_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` WHERE `db_employee`.`employeeid` = '$employeeid'";
$result = $db->query($sql);
if($_POST['submit']){
	$employeeid = $_POST['employeeid'];
	$password = md5($_POST['password'].ALL_PW);
	$sql = "UPDATE `db_employee` SET `password` = '$password' WHERE `employeeid` = '$employeeid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:?id=".$employeeid."&do_status=S");
	}else{
		header("location:?id=".$employeeid."&do_status=F");
	}
}
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
		var password = $("#password").val();
		if(password.length < 6){
			$("#password").focus();
			return false;
		}
	})
})
</script>
<title>系统设置-希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="table_sheet">
  <?php
  if($result->num_rows){
	  $array = $result->fetch_assoc();
	  $do_status = $_GET['do_status'];
	  if($do_status == 'S'){
		  $do_status_info = "设置密码成功！";
	  }elseif($do_status == 'F'){
		  $do_status_info = "设置密码失败，请重新输入新密码！";
	  }else{
		  $do_status_info = "";
	  }
  ?>
  <h4>账户密码设置</h4>
  <form action="" name="password" method="post">
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
        <th>账户：</th>
        <td><?php echo $array['account']; ?></td>
      </tr>
      <tr>
        <th>密码：</th>
        <td><input type="password" name="password" id="password" class="input_txt" />
          <span class="tag"> *密码长度至少6位</span></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="javascript:history.go(-1);" />
          <input type="hidden" name="employeeid" value="<?php echo $employeeid; ?>" />
          <span class="tag"><?php echo $do_status_info; ?></span></td>
      </tr>
    </table>
  </form>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>