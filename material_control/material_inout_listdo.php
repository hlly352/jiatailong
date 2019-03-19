<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_id = $_POST['id'];
	$inoutid = fun_convert_checkbox($array_id);
	$sql_list = "SELECT `listid`,`quantity`,`dotype` FROM `db_material_inout` WHERE `inoutid` IN ($inoutid)";
	$result_list = $db->query($sql_list);
	if($result_list->num_rows){
		while($row_list = $result_list->fetch_assoc()){
			$quantity = $row_list['quantity'];
			$listid = $row_list['listid'];
			$dotype = $row_list['dotype'];
			if($dotype == 'I'){
				$sql_update = "UPDATE `db_material_order_list` SET `in_quantity` = `in_quantity` - '$quantity',`order_surplus` = `order_surplus` - '$quantity' WHERE `listid` = '$listid'";
			}elseif($dotype == 'O'){
				$sql_update = "UPDATE `db_material_order_list` SET `order_surplus` = `order_surplus` + '$quantity' WHERE `listid` = '$listid'";
			}
			$db->query($sql_update);
		}
	}
	$sql_entry = "DELETE FROM `db_godown_entry_list` WHERE `inoutid` IN ($inoutid)";
	$db->query($sql_entry);
	$sql = "DELETE FROM `db_material_inout` WHERE `inoutid` IN ($inoutid)";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>