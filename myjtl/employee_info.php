<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$sql_employee = "SELECT `db_employee`.`employee_name`,`db_employee`.`account`,`db_employee`.`phone`,`db_employee`.`extnum`,`db_employee`.`email`,`db_department`.`dept_name`,`db_personnel_position`.`position_name` FROM `db_employee` INNER JOIN `db_department` ON `db_department`.`deptid` = `db_employee`.`deptid` INNER JOIN `db_personnel_position` ON `db_personnel_position`.`positionid` = `db_employee`.`positionid` WHERE `db_employee`.`employeeid` = '$employeeid'";
$result_employee = $db->query($sql_employee);
if($_POST['submit']){
	$employeeid = $_POST['employeeid'];
	$phone = trim($_POST['phone']);
	$extnum = trim($_POST['extnum']);
	$sql = "UPDATE `db_employee` SET `phone` = '$phone',`extnum` = '$extnum' WHERE `employeeid` = '$employeeid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:");
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
		var phone = $("#phone").val();
		if(!$.trim(phone)){
			$("#phone").focus();
			return false;
		}
	})
})
</script>
<title>我的希尔林</title>
</head>

<body>
<?php include "header.php"; ?>
<div id="myjtl_tag">
  <h4><a href="/myjtl/">MY JTL</a> >> 员工信息</h4>
</div>
<div id="myjtl_table_sheet">
  <?php
  if($result_employee->num_rows){
	  $array_employee = $result_employee->fetch_assoc();
  ?>
  <h4>员工信息修改</h4>
  <form action="" name="employee_info" method="post">
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
        <th>部门：</th>
        <td><?php echo $array_employee['dept_name']; ?></td>
      </tr>
      <tr>
        <th>职位：</th>
        <td><?php echo $array_employee['position_name']; ?></td>
      </tr>
      <tr>
        <th>邮箱：</th>
        <td><?php echo $array_employee['email']; ?></td>
      </tr>
      <tr>
        <th>联系电话：</th>
        <td><input type="text" name="phone" id="phone" value="<?php echo $array_employee['phone']; ?>" class="input_txt" />
          <span class="tag"> *不能为空</span></td>
      </tr>
      <tr>
        <th>座机分机：</th>
        <td><input type="text" name="extnum" value="<?php echo $array_employee['extnum']; ?>" class="input_txt" /></td>
      </tr>
      <tr>
        <th>&nbsp;</th>
        <td><input type="submit" name="submit" id="submit" value="确定" class="button" />
          <input type="button" name="button" value="返回" class="button" onclick="location.href='/myjtl/'" />
          <input type="hidden" name="employeeid" id="employeeid" value="<?php echo $employeeid; ?>" /></td>
      </tr>
    </table>
  </form>
  <?php } ?>
</div>
<?php include "../footer.php"; ?>
</body>
</html>