<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$material_type = $_POST['material_type'];
$sql = "SELECT * FROM `db_other_material_data` WHERE `material_typeid` = '$material_type'";
$result = $db->query($sql);
if($result->num_rows){
	$info = array();
	while($row = $result->fetch_assoc()){
		$info[] = $row;
	}
}
echo json_encode($info);
?>