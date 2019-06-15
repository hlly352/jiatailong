<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
$action = $_GET['action'];
$specification_id = $_GET['specification_id'];

//执行添加操作

if($action == 'approval'){
	$sql = "UPDATE `db_mould_specification` SET `is_approval`='1' WHERE `mould_specification_id`=".$specification_id;
	$db->query($sql);
	if($db->affected_rows){
		header('location:project_approval.php');
	} else {
		header('location:project_approval.php');
	}
	}

?>