<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$supplier_typecode = trim($_POST['supplier_typecode']);
$action = $_POST['action'];
if($action == "add"){
	$sql = "SELECT * FROM `db_supplier_type` WHERE `supplier_typecode` = '$supplier_typecode'";
}elseif($action == "edit"){
	$supplier_typeid = $_POST['supplier_typeid'];
	$sql = "SELECT * FROM `db_supplier_type` WHERE `supplier_typecode` = '$supplier_typecode' AND `supplier_typeid` != '$supplier_typeid'";
}
$result = $db->query($sql);
if($result->num_rows){
	echo 0;
}else{
	echo 1;
}
?>