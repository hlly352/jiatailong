<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_id = $_POST['id'];
	$lvid = fun_convert_checkbox($array_id);
	$sql = "SELECT `leaveid`,`overtimeid`,`deduction_time` FROM `db_leave_overtime` WHERE `lvid` IN ($lvid)";
	$result = $db->query($sql);
	if($result->num_rows){
		while($row = $result->fetch_assoc()){
			$leaveid = $row['leaveid'];
			$overtimeid = $row['overtimeid'];
			$deduction_time = $row['deduction_time'];
			$sql_update_leave = "UPDATE `db_employee_leave` SET `leavetime_valid` = `leavetime_valid` + '$deduction_time' WHERE `leaveid` = '$leaveid'";
			$db->query($sql_update_leave);
			$sql_update_over = "UPDATE `db_employee_overtime` SET `overtime_valid` = `overtime_valid` + '$deduction_time' WHERE `overtimeid` = '$overtimeid'";
			$db->query($sql_update_over);
		}
	}
	$sql_lv = "DELETE FROM `db_leave_overtime` WHERE `lvid` IN ($lvid)";
	$db->query($sql_lv);
	header("location:".$_SERVER['HTTP_REFERER']);
}
?>