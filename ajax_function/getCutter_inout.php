<?php
	require_once '../global_mysql_connect.php';
	//获取cutterid
	$cutterid = $_POST['cutterid'];
	//通过id查询对应的出入库记录
	$sql = "SELECT `db_cutter_inout`.`inoutid`,`db_cutter_inout`.`quantity`,`db_cutter_inout`.`start_quantity`,`db_cutter_inout`.`dotype`,`db_cutter_inout`.`end_quantity`,`db_cutter_hardness`.`hardness`,`db_cutter_specification`.`specification`,`db_cutter_type`.`type` FROM `db_cutter_purchase_list` INNER JOIN `db_cutter_order_list` ON `db_cutter_purchase_list`.`purchase_listid`= `db_cutter_order_list`.`purchase_listid` INNER JOIN `db_cutter_inout` ON `db_cutter_inout`.`listid` = `db_cutter_order_list`.`listid` INNER JOIN `db_mould_cutter` ON `db_cutter_purchase_list`.`cutterid` = `db_mould_cutter`.`cutterid` INNER JOIN `db_cutter_specification` ON `db_mould_cutter`.`specificationid` = `db_cutter_specification`.`specificationid` INNER JOIN `db_cutter_hardness` ON `db_cutter_hardness`.`hardnessid` = `db_mould_cutter`.`hardnessid` INNER JOIN `db_cutter_type` ON `db_cutter_specification`.`typeid` = `db_cutter_type`.`typeid` WHERE `db_cutter_purchase_list`.`cutterid`= ".$cutterid;
	$result = $db->query($sql);
	$inout_list = [];
	if($result->num_rows){
		while($row = $result->fetch_assoc()){
			//排除为null的值
			foreach($row as $k=>$v){
				if(is_null($v)){
					$row[$k] = '';
				}
			}
			$inout_list[] = $row;
 		}
	}
	echo json_encode($inout_list);
	

?>