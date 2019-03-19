<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	if($action == "add"){
		$entry_date = $_POST['entry_date'];
		$sql_number = "SELECT MAX((SUBSTRING(`entry_number`,-2)+0)) AS `max_number` FROM `db_godown_entry` WHERE `entry_date` = '$entry_date'";
		$result_number = $db->query($sql_number);
		if($result_number->num_rows){
			$array_number = $result_number->fetch_assoc();
			$max_number = $array_number['max_number'];
			$next_number = $max_number + 1;
			$entry_number = date('Ymd',strtotime($entry_date)).strtolen($next_number,2).$next_number;
		}else{
			$entry_number = date('Ymd',strtotime($entry_date))."01";
		} 
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
		$sql = "INSERT INTO `db_godown_entry` (`entryid`,`entry_number`,`entry_date`,`employeeid`,`dotype`,`dotime`) VALUES (NULL,'$entry_number','$entry_date','$employeeid','M','$dotime')";
		$db->query($sql);
		if($db->insert_id){
			header("location:material_godown_entry.php");
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$entryid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_godown_entry` WHERE `entryid` IN ($entryid)";
		$db->query($sql);
		if($db->affected_rows){
			header('location:'.$_SERVER['HTTP_REFERER']);
		}
	}
}
?>