<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$material_type = $_POST['material_type'];
$dataid = $_POST['dataid'];
$specificationid = $_POST['specificationid'];
if($specificationid){
	$sql = "SELECT `standard_stock`,`stock` FROM `db_other_material_specification` WHERE `specificationid` = '$specificationid'";

	$result = $db->query($sql);
	if($result->num_rows){
		$info = $result->fetch_assoc();
	}
}
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
	$sql = "SELECT * FROM `db_other_material_data` INNER JOIN `db_other_material_specification` ON `db_other_material_data`.`dataid` = `db_other_material_specification`.`materialid` WHERE `db_other_material_data`.`dataid` = '$dataid'";
	$result = $db->query($sql);
	if($result->num_rows){
		while($row = $result->fetch_assoc()){
			$info[] = $row;
		}
	}
	
}
echo json_encode($info);
?>