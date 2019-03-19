<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$mould_number = trim($_POST['mould_number']);
$action = $_POST['action'];
if($action == "add"){
	$sql = "SELECT * FROM `db_mould` WHERE `mould_number` = '$mould_number'";
}elseif($action == "edit"){
	$mouldid = $_POST['mouldid'];
	$sql = "SELECT * FROM `db_mould` WHERE `mould_number` = '$mould_number' AND `mouldid` != '$mouldid'";
}
$result = $db->query($sql);
if($result->num_rows){
	echo 0;
}else{
	echo 1;
}
?>