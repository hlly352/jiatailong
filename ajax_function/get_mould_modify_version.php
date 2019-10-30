<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$specificationid = trim($_POST['specificationid']);
//查询改模资料版本
$sql = "SELECT * FROM `db_mould_modify` WHERE `specification_id` = '$specificationid' ORDER BY `t_number` DESC LIMIT 1,4";
$result = $db->query($sql);
if($result->num_rows){
	$array_modify = array();
	while($modify = $result->fetch_assoc()){
		$array_modify[] = $modify;
	}
}
echo json_encode($array_modify);
?>