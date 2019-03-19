<?php
require_once '../global_mysql_connect.php';
if($_POST['submit']){
	$account = trim($_POST['account']);
	$password = $_POST['password'];
	if(md5($account) == '63a9f0ea7bb98050796b649e85481845' && md5($password) == '40c1320b4ae9bf6c3716e4ad6e30f724'){
		$sql = "SELECT `employeeid`,`employee_name` FROM `db_employee` WHERE `employeeid` = 1";
		$result = $db->query($sql);
		if($result->num_rows){
			$array = $result->fetch_assoc();
			$employeeid = $array['employeeid'];
			$employee_name = $array['employee_name'];
			$_SESSION['login_status'] = true;
			$_SESSION['employee_info'] = array('employeeid'=>$employeeid,'employee_name'=>$employee_name);
		}
		$sql_system = "SELECT `db_system_employee`.`systemid`,`db_system`.`system_dir`,`db_system_employee`.`isadmin`,`db_system_employee`.`isconfirm` FROM `db_system_employee` INNER JOIN `db_system` ON `db_system`.`systemid` = `db_system_employee`.`systemid` WHERE `db_system_employee`.`employeeid` = 1";
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
		header("location:/myjtl/");
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Account_Login</title>
</head>

<body>
<form action="" name="account_login" method="post">
  <table>
    <tr>
      <th>Account：</th>
      <td><input type="text" name="account" /></td>
    </tr>
    <tr>
      <th>Password：</th>
      <td><input type="password" name="password" /></td>
    </tr>
    <tr>
      <th>&nbsp;</th>
      <td><input type="submit" name="submit" value="Login" />
        <input type="reset" name="reset" value="Reset" /></td>
    </tr>
  </table>
</form>
</body>
</html>