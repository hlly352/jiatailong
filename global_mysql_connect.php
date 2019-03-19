<?php
session_start();
require_once 'config/config.php';
$db = @new mysqli($db_host,$db_user,$db_pw,$db_dataname);
if(mysqli_connect_errno()){
	echo "db connect error:" . mysqli_connect_error();
	$db = null;
	exit;
}
$db->set_charset($db_chareset);
?>