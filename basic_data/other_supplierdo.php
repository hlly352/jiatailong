<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];
if($_POST['submit']){
	$action = $_POST['action'];
	$supplier_cname = trim($_POST['supplier_cname']);
	$supplier_ename = trim($_POST['supplier_ename']);
	$supplier_name = trim($_POST['supplier_name']);
	$supplier_address = trim($_POST['supplier_address']);
	$supplier_type = fun_convert_checkbox($_POST['supplier_type']);
	if($action == "add"){
		$supplier_code = getfirstchar($supplier_cname);
		$time = time();
		$sql = "INSERT INTO `db_other_supplier` (`other_supplier_id`,`supplier_cname`,`supplier_code`,`supplier_ename`,`supplier_name`,`supplier_address`,`supplier_type`,`add_time`,`employeeid`) VALUES (NULL,'$supplier_cname','$supplier_code','$supplier_ename','$supplier_name','$supplier_address','$supplier_type',\"$time\",'$employeeid')";
		$db->query($sql);
		if($db->insert_id){
			header("location:other_material_supplier.php");
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$supplierid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_supplier` WHERE `supplierid` IN ($supplierid)";
		$db->query($sql);
		if($db->affected_rows){
			//header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "edit"){
		$supplierid = $_POST['supplierid'];
		$supplier_code = getfirstchar($supplier_cname);
		$time = time();
		$sql = "UPDATE `db_other_supplier` SET `supplier_cname`='$supplier_cname',`supplier_code`='$supplier_code',`supplier_ename`='$supplier_ename',`supplier_name`='$supplier_name',`supplier_address`='$supplier_address',`supplier_type`='$supplier_type',`employeeid`='$employeeid' WHERE `other_supplier_id`=".$supplierid;
		$db->query($sql);
		if($db->affected_rows){
			header("location:other_material_supplier.php");
		}
	}
}
?>