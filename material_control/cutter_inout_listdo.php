<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_id = $_POST['id'];
	$inoutid = fun_convert_checkbox($array_id);
	$sql_list = "SELECT `apply_listid`,`listid`,`quantity`,`dotype` FROM `db_cutter_inout` WHERE `inoutid` IN ($inoutid)";
	$result_list = $db->query($sql_list);
	if($result_list->num_rows){
		while($row_list = $result_list->fetch_assoc()){
			$apply_listid = $apply_listid['apply_listid'];
			$listid = $row_list['listid'];
			$quantity = $row_list['quantity'];
			$dotype = $row_list['dotype'];
			if($dotype == 'I'){
				$sql_order_update = "UPDATE `db_cutter_order_list` SET `in_quantity` = `in_quantity` - '$quantity',`surplus` = `surplus` - '$quantity' WHERE `listid` = '$listid'";
			}elseif($dotype == 'O'){
				$sql_order_update = "UPDATE `db_cutter_order_list` SET `surplus` = `surplus` + '$quantity' WHERE `listid` = '$listid'";
				$sql_apply_update = "UPDATE `db_cutter_apply_list` SET `out_quantity` = `out_quantity` - '$quantity' WHERE `apply_listid` = '$apply_listid'";
			}
			$db->query($sql_order_update);
			$db->query($sql_apply_update);
		}
	}
	$sql = "DELETE FROM `db_cutter_inout` WHERE `inoutid` IN ($inoutid)";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>