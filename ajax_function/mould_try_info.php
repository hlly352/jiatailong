<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
if($_SERVER['HTTP_REFERER']){
	$mouldid = $_POST['mouldid'];
	//读取模具试模数据，如果存在读取最后一次试模数据，如果不存在(第一次)读取模具数据。
	$sql_try = "SELECT `db_mould_try`.`mould_size`,`db_mould_try`.`plan_date`,`db_mould_try`.`try_times`,`db_mould_try`.`try_causeid`,`db_mould_try`.`tonnage`,`db_mould_try`.`molding_cycle`,`db_mould_try`.`plastic_material_color`,`db_mould_try`.`plastic_material_offer`,`db_mould_try`.`product_weight`,`db_mould_try`.`product_quantity`,`db_mould_try`.`material_weight`,`db_mould_try`.`approver`,`db_mould_try`.`remark`,`db_mould`.`project_name`,`db_mould`.`cavity_number`,`db_mould`.`difficulty_degree`,`db_mould`.`plastic_material`,`db_mould`.`assembler`,`db_client`.`client_code` FROM `db_mould_try` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` WHERE `tryid` = (SELECT MAX(`tryid`) FROM `db_mould_try` WHERE `mouldid` = '$mouldid' AND `try_status` = 1)";
	$result_try = $db->query($sql_try);
	if($result_try->num_rows){
		$array_try = $result_try->fetch_assoc();
		$client_code = $array_try['client_code'];
		$project_name = $array_try['project_name'];
		$mould_size = $array_try['mould_size'];
		$cavity_number = $array_try['cavity_number'];
		$difficulty_degree = $array_try['difficulty_degree'];
		$try_times = $array_try['try_times']+1;
		$try_causeid = $array_try['try_causeid'];
		$tonnage = $array_try['tonnage'];
		$molding_cycle = $array_try['molding_cycle'];
		$plastic_material = $array_try['plastic_material'];
		$plastic_material_color = $array_try['plastic_material_color'];
		$plastic_material_offer = $array_try['plastic_material_offer'];
		$product_weight = $array_try['product_weight'];
		$product_quantity = $array_try['product_quantity'];
		$material_weight = $array_try['material_weight'];
		$plan_date = $array_try['plan_date'];
		$assembler = $array_mould_assembler[$array_try['assembler']];
		$approver = $array_try['approver'];
		$remark = $array_try['remark'];
	}else{
		$sql_mould = "SELECT `db_mould`.`project_name`,`db_mould`.`cavity_number`,`db_mould`.`difficulty_degree`,`db_mould`.`plastic_material`,`db_mould`.`assembler`,`db_client`.`client_code` FROM `db_mould` INNER JOIN `db_client` ON `db_client`.`clientid` = `db_mould`.`clientid` WHERE `mouldid` = '$mouldid'";
		$result_mould = $db->query($sql_mould);
		if($result_mould->num_rows){
			$array_mould = $result_mould->fetch_assoc();
			$client_code = $array_mould['client_code'];
			$project_name = $array_mould['project_name'];
			$cavity_number = $array_mould['cavity_number'];
			$difficulty_degree = $array_mould['difficulty_degree'];
			$plastic_material = $array_mould['plastic_material'];
			$assembler = $array_mould_assembler[$array_mould['assembler']];
		}
	}
	echo $client_code.'#'.$project_name.'#'.$mould_size.'#'.$cavity_number.'#'.$difficulty_degree.'#'.$try_times.'#'.$try_causeid.'#'.$tonnage.'#'.$molding_cycle.'#'.$plastic_material.'#'.$plastic_material_color.'#'.$plastic_material_offer.'#'.$product_weight.'#'.$product_quantity.'#'.$material_weight.'#'.$plan_date.'#'.$assembler.'#'.$approver.'#'.$remark;
}
?>