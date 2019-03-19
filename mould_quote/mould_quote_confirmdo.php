<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$quoteid = $_POST['quoteid'];
	$quote_status = $_POST['quote_status'];
	$total_price_txn = $_POST['total_price_txn'];
	$sql = "UPDATE `db_mould_quote` SET `quote_status` = '$quote_status',`total_price_txn` = '$total_price_txn' WHERE `quoteid` = '$quoteid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_POST['pre_url']);
	}
}
?>