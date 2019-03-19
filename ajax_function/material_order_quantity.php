<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$materialid = $_POST['materialid'];
$sql = "SELECT SUBSTRING(`material_number`,1,1) AS `material_number`,`specification`,`material_quantity` FROM `db_mould_material` WHERE `materialid` = '$materialid'";
$result = $db->query($sql);
if($result->num_rows){
	$array = $result->fetch_assoc();
	$material_number = $array['material_number'];
	$specification = $array['specification'];
	$material_quantity = $array['material_quantity'];
	$array_material_type = array(1,2,3,4,5);
	if(in_array($material_number,$array_material_type)){
		$unit = 3;
		if(strpos($specification,'#') !==  false){
			$array_specification = explode('#',$specification);
			$material_size = $array_specification[0];
			$array_size = explode('*',$material_size);
			if(count($array_size)){
				$length = $array_size[0];
				$width = $array_size[1];
				$height = $array_size[2];
				echo round($material_quantity*(($length+5)*($width+5)*($height+5)*7850/(1000*1000*1000)),2).'#'.$unit;
			}
		}else{
			$unit = 1;
			echo $material_quantity.'#'.$unit;
		}
	}elseif($material_number == 9){
		$unit = 3;
		echo $specification.'#'.$unit;
	}else{
		$unit = 1;
		echo $material_quantity.'#'.$unit;
	}
}
?>