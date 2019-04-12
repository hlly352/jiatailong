<?php 
	require_once '../global_mysql_connect.php';
	require_once '../function/function.php';


	$mold_id = trim($_POST['mold_id']);
	$sql = "SELECT * FROM `db_mould_data` WHERE `mold_id` = '$mold_id'";

	$res = $db->query($sql);
	$info = [];
	while($re = $res->fetch_assoc()){
		$info[] = $re;
		
	}
	echo json_encode($info);
?>