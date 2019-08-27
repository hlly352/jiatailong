<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$supplier_cname = trim($_POST['supplier_cname']);
	$supplier_ename = trim($_POST['supplier_ename']);
	$supplier_name = trim($_POST['supplier_name']);
	$supplier_address = trim($_POST['supplier_address']);
	$supplier_blank = trim($_POST['supplier_blank']);
	$supplier_account  = trim($_POST['supplier_account']);
	$supplier_typeid = fun_convert_checkbox($_POST['supplier_typeid']);
	$business_typeid = fun_convert_checkbox($_POST['business_typeid']);
	if($action == "add"){
		$supplier_code = getfirstchar($supplier_cname);
		$sql = "INSERT INTO `db_supplier` (`supplierid`,`supplier_cname`,`supplier_code`,`supplier_ename`,`supplier_name`,`supplier_address`,`supplier_typeid`,`business_typeid`,`supplier_status`,`supplier_blank`,`supplier_account`) VALUES (NULL,'$supplier_cname','$supplier_code','$supplier_ename','$supplier_name','$supplier_address','$supplier_typeid','$business_typeid',1,'$supplier_blank','$supplier_account')";
		$db->query($sql);
		if($db->insert_id){
			header("location:supplier.php");
		}
	}elseif($action == "edit"){
		$supplierid = $_POST['supplierid'];
		$supplier_code = strtoupper(trim($_POST['supplier_code']));
		$supplier_status = $_POST['supplier_status'];
		$sql = "UPDATE `db_supplier` SET `supplier_cname` = '$supplier_cname',`supplier_code` = '$supplier_code',`supplier_ename` = '$supplier_ename',`supplier_name` = '$supplier_name',`supplier_address` = '$supplier_address',`supplier_typeid` = '$supplier_typeid',`business_typeid` = '$business_typeid',`supplier_status` = '$supplier_status',`supplier_blank` = '$supplier_blank',`supplier_account` = '$supplier_account' WHERE `supplierid` = '$supplierid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:supplier.php");
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$supplierid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_supplier` WHERE `supplierid` IN ($supplierid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>