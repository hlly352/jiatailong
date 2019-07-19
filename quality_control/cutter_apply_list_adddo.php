<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$applyid = $_POST['applyid'];
	$array_cutterid = $_POST['cutterid'];
	$array_quantity = $_POST['quantity'];
	$array_mouldid = $_POST['mouldid'];
	$array_plan_date = $_POST['plan_date'];
	$array_remark = $_POST['remark'];
	foreach($array_cutterid as $key=>$cutterid){
		$quantity = $array_quantity[$key];
		$mouldid = $array_mouldid[$key];
		$plan_date = $array_plan_date[$key];
		$remark = trim($array_remark[$key]);
		if($quantity && $mouldid){
			$sql_list .= "(NULL,'$applyid','$cutterid','$quantity','$mouldid','$plan_date','$remark'),";
		}
	}
	$sql_list = rtrim($sql_list,',');
	$sql = "INSERT INTO `db_cutter_apply_list` (`apply_listid`,`applyid`,`cutterid`,`quantity`,`mouldid`,`plan_date`,`remark`) VALUES $sql_list";
	$db->query($sql);
	if($db->insert_id){
		header("location:cutter_apply_list.php?applyid=".$applyid);
	}
}
?>