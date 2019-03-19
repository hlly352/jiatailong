<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$account = trim($_POST['account']);
	$email = trim($_POST['email']);
	$account_status = $_POST['account_status'];
	$employeeid = $_POST['employeeid'];
	//关闭账号,删除应用程序成员权限
	if($account_status == 0){
		$sql_system_employee = "DELETE FROM `db_system_employee` WHERE `employeeid` = '$employeeid'";
		$db->query($sql_system_employee);
	}
	$sql = "UPDATE `db_employee` SET `account` = '$account',`email` = '$email',`account_status` = '$account_status' WHERE `employeeid` = '$employeeid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
if($_POST['submit_password']){
	$employeeid = $_POST['employeeid'];
	//生成随机8位密码
	for($i=0;$i<8;$i++){
		$rand .= dechex(rand(1,16));
	}
	$password = md5($rand.ALL_PW);
	$sql_password = "UPDATE `db_employee` SET `password` = '$password' WHERE `employeeid` = '$employeeid' AND `account_status` = 1";
	$db->query($sql_password);
	if($db->affected_rows){
		$sql_employee = "SELECT `employee_name`,`account`,`email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
		$result_employee = $db->query($sql_employee);
		if($result_employee->num_rows){
			$array_employee = $result_employee->fetch_assoc();
			$employee_name = $array_employee['employee_name'];
			$account = $array_employee['account'];
			$email_name = $array_employee['email'];
			$email_subject = "账号密码重置信息";
			$email_content = $employee_name.'，您的账号'.$account."密码被管理员重置，新密码为".$rand."，为保证账号安全，请及时登录系统修改密码。";
			$dotime = fun_gettime();
			$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
			$db->query($sql_email);
			if($db->insert_id){
				header("location:".$_SERVER['HTTP_REFERER']);
			}
		}	
	}
}
?>