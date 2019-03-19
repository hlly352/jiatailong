<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if (isset($_POST['submit'])) {
	$mouldid = $_POST['mouldid'];
	$part_number = trim($_POST['part_number']);
	$order_date = $_POST['order_date'];
	$workteamid = $_POST['workteamid'];
	$order_number = trim($_POST['order_number']);
	$quantity = $_POST['quantity'];
	$outward_typeid = $_POST['outward_typeid'];
	$applyer = trim($_POST['applyer']);
	$plan_date = $_POST['plan_date'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	$sql = "INSERT INTO `db_mould_outward` (`outwardid`,`part_number`,`order_date`,`workteamid`,`order_number`,`quantity`,`outward_typeid`,`applyer`,`plan_date`,`outward_status`,`mouldid`,`employeeid`,`dotime`) VALUES (NULL,'$part_number','$order_date','$workteamid','$order_number','$quantity','$outward_typeid','$applyer','$plan_date',1,'$mouldid','$employeeid','$dotime')";
	$db->query($sql);
	if ($db->insert_id) {
		header("location:mould_outward.php");
	}
}
?>