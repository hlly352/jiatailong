<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$material_typecode = trim($_POST['material_typecode']);
$action = $_POST['action'];
if($action == "add"){
	$sql = "SELECT * FROM `db_other_material_type` WHERE `material_typecode` = '$material_typecode'";
}elseif($action == "edit"){
	$material_typeid = $_POST['material_typeid'];
	$sql = "SELECT * FROM `db_other_material_type` WHERE `material_typecode` = '$material_typecode' AND `material_typeid` != '$material_typeid'";
}
$result = $db->query($sql);
if($result->num_rows){
	echo 0;
}else{
	echo 1;
}
?>