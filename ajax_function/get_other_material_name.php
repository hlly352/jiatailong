<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$material_type = $_POST['material_type'];
$dataid = $_POST['dataid'];
if(empty($dataid)){
	$sql = "SELECT * FROM `db_other_material_data` WHERE `material_typeid` = '$material_type'";
	$result = $db->query($sql);
	if($result->num_rows){
		$info = array();
		while($row = $result->fetch_assoc()){
			$info[] = $row;
		}
	}
}else{
	$sql = "SELECT `standard_stock`,`stock` FROM `db_other_material_data` WHERE `dataid` = '$dataid'";
	$result = $db->query($sql);
	if($result->num_rows){
		$info = $result->fetch_assoc();
	}
	
}
echo json_encode($info);
?>