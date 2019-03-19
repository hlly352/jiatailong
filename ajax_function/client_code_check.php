<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$client_code = trim($_POST['client_code']);
$action = $_POST['action'];
if($action == "add"){
	$sql = "SELECT * FROM `db_client` WHERE `client_code` = '$client_code'";
}elseif($action == "edit"){
	$clientid = $_POST['clientid'];
	$sql = "SELECT * FROM `db_client` WHERE `client_code` = '$client_code' AND `clientid` != '$clientid'";
}
$result = $db->query($sql);
if($result->num_rows){
	echo 0;
}else{
	echo 1;
}
?>