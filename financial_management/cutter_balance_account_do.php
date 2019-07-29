<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$accountid = $_GET['accountid'];
$action = $_GET['action'];
//遍历accountid，更改对账状态
if($action == 'complete'){
	$sql = "UPDATE `db_cutter_inout` SET `account_status` = 'I' WHERE `inoutid` IN (SELECT `inoutid` FROM `db_material_account_list` WHERE `accountid` = '$accountid') ";
	 $db->query($sql);
    if($db->affected_rows){
    	header('location:cutter_balance_account.php');
    	}
} elseif($action == 'back'){
		$sql = "UPDATE `db_cutter_inout` SET `account_status` = 'P' WHERE `inoutid` IN(SELECT `inoutid` FROM `db_material_account_list` WHERE `accountid` = '$accountid')";
		$db->query($sql);
		if($db->affected_rows){
			//删除对账汇总表和对账列表中的信息
			$sql_account = "DELETE FROM `db_material_account` WHERE `accountid`='$accountid'";
			$db->query($sql_account);
			$sql_list = "DELETE FROM `db_material_account_list` WHERE `accountid` = '$accountid'";
			$db->query($sql_list);
			if($db->affected_rows){
				header('location:cutter_balance_account.php');
			}
		}
	}
?>