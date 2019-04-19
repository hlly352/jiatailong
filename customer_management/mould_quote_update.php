<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_SERVER['HTTP_REFERER']){
	$quote_listid = $_POST['quote_listid'];
	$field_name = $_POST['field_name'];
	$field_value = $_POST['field_value'];
	$item_type_sn = $_POST['item_type_sn'];
	$sql_do = "UPDATE `db_mould_quote_list` SET `$field_name` = '$field_value' WHERE `quote_listid` = '$quote_listid'";
	$db->query($sql_do);
	if($db->affected_rows && ($field_name != 'specification' && $field_name != 'supplier')){
		if($item_type_sn == 'A'){
			$sql = "SELECT `quoteid`,`number`,`length`,`width`,`height`,`weight`,`unit_price`,`total_price` FROM `db_mould_quote_list` WHERE `quote_listid` = '$quote_listid'";
			$result = $db->query($sql);
			if($result->num_rows){
				$array = $result->fetch_assoc();
				$quoteid = $array['quoteid'];
				$number = $array['number'];
				$length = $array['length'];
				$width = $array['width'];
				$height = $array['height'];
				$weight = ($length*$width*$height*0.00000785)*$number;
				$unit_price = $array['unit_price'];
				$total_price = $weight*$unit_price;
				$sql_update = "UPDATE `db_mould_quote_list` SET `number` = '$number',`length` = '$length',`width` = '$width',`height` = '$height',`weight` = '$weight',`unit_price` = '$unit_price',`total_price` = '$total_price' WHERE `quote_listid` = '$quote_listid'";
				$db->query($sql_update);
				//统计总价
				$sql_group = "SELECT `db_quote_item_type`.`item_type_sn`,SUM(`db_mould_quote_list`.`total_price`) AS `sum_price` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_mould_quote_list`.`quoteid` = '$quoteid' GROUP BY `db_quote_item_type`.`item_type_sn`";
				$result_group = $db->query($sql_group);
				if($result_group->num_rows){
					while($row_group = $result_group->fetch_assoc()){
						$array_sum_price[$row_group['item_type_sn']] = $row_group['sum_price'];
					}
				}else{
					$array_sum_price = array();
				}
				$sum_price_A = array_key_exists('A',$array_sum_price)?$array_sum_price['A']:0;
				$sum_price_B = array_key_exists('B',$array_sum_price)?$array_sum_price['B']:0;
				/*-----------------------------------------*/
				//统计淬火/Hardened重量
				$sql_B = "SELECT `db_mould_quote_list`.`weight` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_mould_quote_list`.`quoteid` = '$quoteid' AND `db_quote_item_type`.`item_type_sn` = '$item_type_sn' AND `db_quote_item`.`item_sn` IN ('B','C','D','E')";
				$result_B = $db->query($sql_B);
				if($result_B->num_rows){
					while($row_B = $result_B->fetch_assoc()){
						$total_B_B_weight += $row_B['weight'];
					}
				}else{
					$total_B_B_weight = 0;
				}
				//D工时
				$d_hour = round($sum_price_A*0.15/100,0);
				//E工时
				$e_hour = round(($sum_price_A+$sum_price_B)*1.5/100/10,0);
				/*-------------------------------------------*/
				$sql_push = "SELECT `length`,`width`,`height`,`weight`,`unit_price`,`total_price` FROM `db_mould_quote_list` WHERE `quote_listid` = '$quote_listid'";
				$result_push = $db->query($sql_push);
				if($result_push->num_rows){
					$array_push = $result_push->fetch_assoc();
					echo $array_push['length'].'#'.$array_push['width'].'#'.$array_push['height'].'#'.$array_push['weight'].'#'.$array_push['unit_price'].'#'.number_format($array_push['total_price'],2).'#'.number_format($sum_price_A,2).'#'.$total_B_B_weight.'#'.$d_hour.'#'.$e_hour;
				}
			}
		}elseif($item_type_sn == 'B'){
			$sql = "SELECT `quoteid`,`weight`,`unit_price`,`total_price` FROM `db_mould_quote_list` WHERE `quote_listid` = '$quote_listid'";
			$result = $db->query($sql);
			if($result->num_rows){
				$array = $result->fetch_assoc();
				$quoteid = $array['quoteid'];
				$weight = $array['weight'];
				$unit_price = $array['unit_price'];
				$total_price = $weight*$unit_price;
				$sql_update = "UPDATE `db_mould_quote_list` SET `weight` = '$weight',`unit_price` = '$unit_price',`total_price` = '$total_price' WHERE `quote_listid` = '$quote_listid'";
				$db->query($sql_update);
				//统计总价
				$sql_group = "SELECT `db_quote_item_type`.`item_type_sn`,SUM(`db_mould_quote_list`.`total_price`) AS `sum_price` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_mould_quote_list`.`quoteid` = '$quoteid' GROUP BY `db_quote_item_type`.`item_type_sn`";
				$result_group = $db->query($sql_group);
				if($result_group->num_rows){
					while($row_group = $result_group->fetch_assoc()){
						$array_sum_price[$row_group['item_type_sn']] = $row_group['sum_price'];
					}
				}else{
					$array_sum_price = array();
				}
				$sum_price_A = array_key_exists('A',$array_sum_price)?$array_sum_price['A']:0;
				$sum_price_B = array_key_exists('B',$array_sum_price)?$array_sum_price['B']:0;
				//E工时
				$e_hour = round(($sum_price_A+$sum_price_B)*1.5/100/10,0);
				$sql_push = "SELECT `weight`,`unit_price`,`total_price` FROM `db_mould_quote_list` WHERE `quote_listid` = '$quote_listid'";
				$result_push = $db->query($sql_push);
				if($result_push->num_rows){
					$array_push = $result_push->fetch_assoc();
					echo $array_push['weight'].'#'.$array_push['unit_price'].'#'.number_format($array_push['total_price'],2).'#'.number_format($sum_price_B,2).'#'.$e_hour.'#';
				}
			}
		}elseif($item_type_sn == 'C'){
			$sql = "SELECT `quoteid`,`number`,`unit_price`,`total_price` FROM `db_mould_quote_list` WHERE `quote_listid` = '$quote_listid'";
			$result = $db->query($sql);
			if($result->num_rows){
				$array = $result->fetch_assoc();
				$quoteid = $array['quoteid'];
				$number = $array['number'];
				$unit_price = $array['unit_price'];
				$total_price = $number*$unit_price;
				$sql_update = "UPDATE `db_mould_quote_list` SET `number` = '$number',`unit_price` = '$unit_price',`total_price` = '$total_price' WHERE `quote_listid` = '$quote_listid'";
				$db->query($sql_update);
				$sql_group = "SELECT `db_mould_quote_list`.`total_price` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_mould_quote_list`.`quoteid` = '$quoteid' AND `db_quote_item_type`.`item_type_sn` = '$item_type_sn'";
				$result_group = $db->query($sql_group);
				if($result_group->num_rows){
					while($row_group = $result_group->fetch_assoc()){
						$sum_price += $row_group['total_price'];
					}
				}else{
					$sum_price = 0;
				}
				$sql_push = "SELECT `unit_price`,`total_price` FROM `db_mould_quote_list` WHERE `quote_listid` = '$quote_listid'";
				$result_push = $db->query($sql_push);
				if($result_push->num_rows){
					$array_push = $result_push->fetch_assoc();
					echo $array_push['unit_price'].'#'.number_format($array_push['total_price'],2).'#'.number_format($sum_price,2).'#';
				}
			}
		}elseif($item_type_sn == 'D' || $item_type_sn == 'E'){
			$sql = "SELECT `quoteid`,`hour`,`unit_price`,`total_price` FROM `db_mould_quote_list` WHERE `quote_listid` = '$quote_listid'";
			$result = $db->query($sql);
			if($result->num_rows){
				$array = $result->fetch_assoc();
				$quoteid = $array['quoteid'];
				$hour = $array['hour'];
				$unit_price = $array['unit_price'];
				$total_price = $hour*$unit_price;
				$sql_update = "UPDATE `db_mould_quote_list` SET `hour` = '$hour',`unit_price` = '$unit_price',`total_price` = '$total_price' WHERE `quote_listid` = '$quote_listid'";
				$db->query($sql_update);
				$sql_group = "SELECT `db_mould_quote_list`.`total_price` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_mould_quote_list`.`quoteid` = '$quoteid' AND `db_quote_item_type`.`item_type_sn` = '$item_type_sn'";
				$result_group = $db->query($sql_group);
				if($result_group->num_rows){
					while($row_group = $result_group->fetch_assoc()){
						$sum_price += $row_group['total_price'];
					}
				}else{
					$sum_price = 0;
				}
				$sql_push = "SELECT `unit_price`,`total_price` FROM `db_mould_quote_list` WHERE `quote_listid` = '$quote_listid'";
				$result_push = $db->query($sql_push);
				if($result_push->num_rows){
					$array_push = $result_push->fetch_assoc();
					echo $array_push['unit_price'].'#'.number_format($array_push['total_price'],2).'#'.number_format($sum_price,2).'#';
				}
			}
		}elseif($item_type_sn == 'F'){
			$sql = "SELECT `quoteid`,`total_price` FROM `db_mould_quote_list` WHERE `quote_listid` = '$quote_listid'";
			$result = $db->query($sql);
			if($result->num_rows){
				$array = $result->fetch_assoc();
				$quoteid = $array['quoteid'];
				$total_price_F_list = $array['total_price'];
				echo number_format($total_price_F_list,2).'#';
			}
		}
		if(!in_array($item_type_sn,array('A','B'))){
			$sql_group = "SELECT `db_quote_item_type`.`item_type_sn`,`db_quote_item_type`.`item_typename`,SUM(`db_mould_quote_list`.`total_price`) AS `sum_price` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_mould_quote_list`.`quoteid` = '$quoteid' AND CONCAT(`db_quote_item_type`.`item_type_sn`,`db_quote_item`.`item_sn`) NOT IN ('FC','FD','FE') GROUP BY `db_quote_item_type`.`item_type_sn`";
			$result_group = $db->query($sql_group);
			if($result_group->num_rows){
				while($row_group = $result_group->fetch_assoc()){
					$array_group[$row_group['item_type_sn']] = $row_group['sum_price'];
				}
			}else{
				$array_group = array();
			}
			$total_price = array_sum($array_group);
			$sql_list = "SELECT `db_mould_quote_list`.`quote_listid`,`db_mould_quote_list`.`descripition`,CONCAT(`db_quote_item_type`.`item_type_sn`,`db_quote_item`.`item_sn`) AS `item_type` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_mould_quote_list`.`quoteid` = '$quoteid' AND CONCAT(`db_quote_item_type`.`item_type_sn`,`db_quote_item`.`item_sn`) IN ('FC','FD','FE')";
			$result_list = $db->query($sql_list);
			if($result_list->num_rows){
				while($row_list = $result_list->fetch_assoc()){
					$array_list[$row_list['item_type']] = array('quote_listid'=>$row_list['quote_listid'],'descripition'=>$row_list['descripition']);
				}
			}else{
				$array_list = array();
			}
			//管理费
			$total_FC = round($total_price*((float)$array_list['FC']['descripition']/100),2);
			$quote_listid_FC = $array_list['FC']['quote_listid'];
			$db->query("UPDATE `db_mould_quote_list` SET `total_price` = '$total_FC' WHERE `quote_listid` = '$quote_listid_FC'");
			//利润
			$total_FD = round($total_price*((float)$array_list['FD']['descripition']/100),2);
			$quote_listid_FD = $array_list['FD']['quote_listid'];
			$db->query("UPDATE `db_mould_quote_list` SET `total_price` = '$total_FD' WHERE `quote_listid` = '$quote_listid_FD'");
			//税
			$total_FE = round(($total_price+$total_FC+$total_FD)*((float)$array_list['FE']['descripition']/100),2);
			$quote_listid_FE = $array_list['FE']['quote_listid'];
			$db->query("UPDATE `db_mould_quote_list` SET `total_price` = '$total_FE' WHERE `quote_listid` = '$quote_listid_FE'");
			$total_F = $total_AB+$total_FC+$total_FD+$total_FE;
			//统计不含税
			$total_mould_price = round($total_price+$total_FC+$total_FD,2);
			//统计美元
			$total_mould_price_usd = round($total_mould_price/1.02/6.5,2);
			//统计含税
			$total_mould_price_vat = round($total_mould_price+$total_FE,2);
			$sql_total = "UPDATE `db_mould_quote` SET `total_price` = '$total_mould_price',`total_price_usd` = '$total_mould_price_usd',`total_price_vat` = '$total_mould_price_vat' WHERE `quoteid` = '$quoteid'";
			$db->query($sql_total);
			if($db->affected_rows){
				echo number_format($total_FC,2).'#'.number_format($total_FD,2).'#'.number_format($total_FE,2).'#'.number_format($total_F,2).'#¥'.number_format($total_mould_price,2).'#$'.number_format($total_mould_price_usd,2).'#¥'.number_format($total_mould_price_vat,2).'#'.$total_a;
			}
		}
	}
}
?>