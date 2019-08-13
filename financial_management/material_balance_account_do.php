<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$accountid = $_GET['accountid'];
$action = $_GET['action'];
//遍历accountid，更改对账状态
if($action == 'complete'){
	//更改对账汇总表的状态
	$sql = "UPDATE `db_material_account` SET `status` = 'I' WHERE `accountid` = '$accountid'";
	 $db->query($sql);
    if($db->affected_rows){
    	header('location:material_balance_account.php');
    	}
} elseif($action == 'back'){
			//删除对账汇总表和对账列表中的信息
			$sql_account = "DELETE FROM `db_material_account` WHERE `accountid`='$accountid'";
			$db->query($sql_account);
			$sql_list = "DELETE FROM `db_material_account_list` WHERE `accountid` = '$accountid'";
			$db->query($sql_list);
			if($db->affected_rows){
				header('location:material_balance_account.php');
			}
		
	}
?>