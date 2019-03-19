<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_id = $_POST['id'];
	$outid = fun_convert_checkbox($array_id);
	$sql_list = "SELECT `db_cutter_out`.`listid`,`db_cutter_out`.`quantity`,`db_cutter_out`.`cutterid` FROM `db_cutter_out` INNER JOIN `db_cutter_apply_list` ON `db_cutter_apply_list`.`listid` = `db_cutter_out`.`listid` WHERE `db_cutter_out`.`outid` IN ($outid)";
	$result_list = $db->query($sql_list);
	if($result_list->num_rows){
		while($row_list = $result_list->fetch_assoc()){
			$quantity = $row_list['quantity'];
			$listid = $row_list['listid'];
			$cutterid = $row_list['cutterid'];	
			$sql_update = "UPDATE `db_cutter_apply_list` SET `out_quantity` = `out_quantity` - '$quantity' WHERE `listid` = '$listid'";
			$db->query($sql_update);
			if($db->affected_rows){
				$sql_update = "UPDATE `db_mould_cutter` SET `surplus` = `surplus` + '$quantity' WHERE `cutterid` = '$cutterid'";
				$db->query($sql_update);
			}
		}
	}
	$sql = "DELETE FROM `db_cutter_out` WHERE `outid` IN ($outid)";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>