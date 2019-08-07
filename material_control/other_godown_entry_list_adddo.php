<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$entryid = $_POST['entryid'];
	$array_inoutid = $_POST['id'];
	foreach($array_inoutid as $inoutid){
		$sqladd .= "(NULL,'$entryid','$inoutid'),";
	}
	$sqladd = rtrim($sqladd,',');
	$sql = "INSERT INTO `db_godown_entry_list` VALUES $sqladd";
	$db->query($sql);
	if($db->insert_id){
		header("location:other_godown_entry_list.php?entryid=".$entryid);
	}
}
?>