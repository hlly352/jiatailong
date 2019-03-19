<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_id = $_POST['id'];
	$inid = fun_convert_checkbox($array_id);
	$sql_list = "SELECT `db_cutter_in`.`quantity`,`db_cutter_in`.`listid`,`db_mould_cutter`.`cutterid` FROM `db_cutter_in` INNER JOIN `db_cutter_order_list` ON `db_cutter_order_list`.`listid` = `db_cutter_in`.`listid` INNER JOIN `db_cutter_purchase_list` ON `db_cutter_purchase_list`.`purchase_listid` = `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_mould_cutter` ON `db_mould_cutter`.`cutterid` = `db_cutter_purchase_list`.`cutterid` WHERE `db_cutter_in`.`inid` IN ($inid) AND `db_mould_cutter`.`surplus` >= `db_cutter_in`.`quantity`";
	$result_list = $db->query($sql_list);
	if($result_list->num_rows){
		while($row_list = $result_list->fetch_assoc()){
			$quantity = $row_list['quantity'];
			$listid = $row_list['listid'];
			$cutterid = $row_list['cutterid'];
			$sql_update = "UPDATE `db_cutter_order_list` SET `in_quantity` = `in_quantity` - '$quantity' WHERE `listid` = '$listid'";
			$db->query($sql_update);
			if($db->affected_rows){
				$sql_update = "UPDATE `db_mould_cutter` SET `surplus` = `surplus` - '$quantity' WHERE `cutterid` = '$cutterid'";
				$db->query($sql_update);
			}
		}
	}
	$sql_entry = "DELETE FROM `db_godown_entry_list` WHERE `inid` IN ($inid)";
	$db->query($sql_entry);
	$sql = "DELETE FROM `db_cutter_in` WHERE `inid` IN ($inid)";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>