<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$materialid = $_POST['materialid'];

$sql = "SELECT `db_mould_material`.`materialid`,`db_mould_material`.`material_date`,`db_mould_material`.`material_list_number`,`db_mould_material`.`material_list_sn`,`db_mould_material`.`material_number`,`db_mould_material`.`material_name`,`db_mould_material`.`specification`,`db_mould_material`.`material_quantity`,`db_mould_material`.`texture`,`db_mould_material`.`hardness`,`db_mould_material`.`brand`,`db_mould_material`.`spare_quantity`,`db_mould_material`.`remark`,`db_mould_material`.`complete_status`,`db_mould_specification`.`mould_no`,SUBSTRING(`db_mould_material`.`material_number`,1,1) AS `material_number_code` FROM `db_mould_material` INNER JOIN `db_mould_specification` ON `db_mould_specification`.`mould_specification_id` = `db_mould_material`.`mouldid` WHERE `db_mould_material`.`parentid` = '$materialid'";

$result = $db->query($sql);
if($result->num_rows){
	$array_material = [];
	while($row = $result->fetch_assoc()){
		$array_material[] = $row;
	}
	}
echo json_encode($array_material);
?>