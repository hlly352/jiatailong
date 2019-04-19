<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$mould_dataid = $_POST['mould_dataid'];
	$quote_date = fun_getdate();
	$employeeid = $_SESSION['employee_info']['employeeid'];
	//查询模具基本信息
	$sql_mould = "SELECT `cavity_type`,`p_length`,`p_width`,`p_height` FROM `db_mould_data` WHERE `mould_dataid` = '$mould_dataid'";
	$result_mould = $db->query($sql_mould);
	if($result_mould->num_rows){
		$array_mould = $result_mould->fetch_assoc();
		$cavity_type = $array_mould['cavity_type'];
		$p_length = $array_mould['p_length'];
		$p_width = $array_mould['p_width'];
		$p_height = $array_mould['p_height'];
	}else{
		die('错误');
	}
	$sql_num = "SELECT `ver_num` FROM `db_mould_quote` WHERE `mould_dataid` = '$mould_dataid' ORDER BY `ver_num` DESC LIMIT 0,1";
	$result_num = $db->query($sql_num);
	if($result_num->num_rows){
		$array = $result_num->fetch_assoc();
		$next_num = $array['ver_num'] + 1;
	}else{
		$next_num = 1;
	}
	$sql_quote = "INSERT INTO `db_mould_quote` (`quoteid`,`quote_date`,`employeeid`,`ver_num`,`mould_dataid`) VALUE (NULL,'$quote_date','$employeeid','$next_num','$mould_dataid')";
	$db->query($sql_quote);
	if($quoteid = $db->insert_id){
		$sql_item = "SELECT `db_quote_item_type`.`item_type_sn`,`db_quote_item`.`itemid`,`db_quote_item`.`item_sn`,`db_quote_item`.`specification`,`db_quote_item`.`unit_price`,`db_quote_item`.`descripition` FROM `db_quote_item` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` ORDER BY `db_quote_item_type`.`item_type_sn` ASC,`db_quote_item`.`item_sn` ASC";
		$result_item = $db->query($sql_item);
		if($result_item->num_rows){
			while($row_item = $result_item->fetch_assoc()){
				$item_type_sn = $row_item['item_type_sn'];
				$item_sn = $row_item['item_sn'];
				$specification = $row_item['specification'];
				$unit_price = $row_item['unit_price'];
				$descripition = $row_item['descripition'];
				$itemid = $row_item['itemid'];
				$item_type = $item_type_sn.'-'.$item_sn;
				if($item_type_sn == 'A'){
					if(in_array($cavity_type,array('A','C','E'))){
						$c_length = round($p_length*1.6,1);
						$c_width = round($p_width+($p_length*0.6),1);
						$c_height = $p_height+100;	
					}elseif(in_array($cavity_type,array('B','D'))){
						$c_length = $p_length;
						$c_width = round($p_width+($p_length*0.6),1);
						$c_height = $p_height+100;
					}
					if($item_type == 'A-A'){
						$number = 1;
						$length = $c_length+250;
						$width = $c_width+250;
						$height = $c_height+500;
					}elseif($item_type == 'A-B' || $item_type == 'A-C'){
						$number = 1;
						$length = $c_length;
						$width = $c_width;
						$height = $c_height;
					}elseif($item_type == 'A-E'){
						$number = 1;
						$length = round($p_length/2,1);
						$width = round($p_width/2,1);
						$height = $p_height+100;
					}elseif($item_type == 'A-F'){
						$number = 1;
						$length = $p_length;
						$width = $p_width;
						$height = $p_height+40; //未定义
					}else{
						$number = 0;
						$length = 0;
						$width = 0;
						$height = 0;
					}
					$weight = round($length*$width*$height*0.00000785*$number,2);
					$total_price = round($weight*$unit_price,2);
					$sql_list = "INSERT INTO `db_mould_quote_list` (`quote_listid`,`quoteid`,`itemid`,`specification`,`length`,`width`,`height`,`weight`,`number`,`unit_price`,`total_price`) VALUES (NULL,'$quoteid','$itemid','$specification','$length','$width','$height','$weight','$number','$unit_price','$total_price')";
					$db->query($sql_list);
					$sum_A_price += $total_price;
				}elseif($item_type_sn == 'B'){
					if($item_type == 'B-B'){
						//统计淬火/Hardened重量
						$sql_B_B = "SELECT `db_mould_quote_list`.`weight` FROM `db_mould_quote_list` INNER JOIN `db_quote_item` ON `db_quote_item`.`itemid` = `db_mould_quote_list`.`itemid` INNER JOIN `db_quote_item_type` ON `db_quote_item_type`.`item_typeid` = `db_quote_item`.`item_typeid` WHERE `db_mould_quote_list`.`quoteid` = '$quoteid' AND `db_quote_item_type`.`item_type_sn` = 'A' AND `db_quote_item`.`item_sn` IN ('B','C','D','E')";
						$result_B_B = $db->query($sql_B_B);
						if($result_B_B->num_rows){
							while($row_B_B = $result_B_B->fetch_assoc()){
								$total_B_B_weight += $row_B_B['weight'];
							}
						}else{
							$total_B_B_weight = 0;
						}
						$weight = $total_B_B_weight;
					}else{
						$weight = 0;
					}
					$total_price = round($weight*$unit_price,2);
					$sql_list = "INSERT INTO `db_mould_quote_list` (`quote_listid`,`quoteid`,`itemid`,`weight`,`number`,`unit_price`,`total_price`) VALUES (NULL,'$quoteid','$itemid','$weight','$number','$unit_price','$total_price')";
					$db->query($sql_list);
					$sum_B_price += $total_price;
				}elseif($item_type_sn == 'C'){
					$number = 1;
					$total_price = round($number*$unit_price,2);
					$sql_list = "INSERT INTO `db_mould_quote_list` (`quote_listid`,`quoteid`,`itemid`,`number`,`unit_price`,`total_price`) VALUES (NULL,'$quoteid','$itemid','$number','$unit_price','$total_price')";
					$db->query($sql_list);
					$sum_C_price += $total_price;
				}elseif($item_type_sn == 'D'){
					$hour = round($sum_A_price*0.15/100);
					$total_price = round($hour*$unit_price,2);
					$sql_list = "INSERT INTO `db_mould_quote_list` (`quote_listid`,`quoteid`,`itemid`,`hour`,`unit_price`,`total_price`) VALUES (NULL,'$quoteid','$itemid','$hour','$unit_price','$total_price')";
					$db->query($sql_list);
					$sum_D_price += $total_price;
				}elseif($item_type_sn == 'E'){
					$e_hour = round(($sum_A_price+$sum_B_price)*1.5/100/10);
					if($item_sn == 'A' || $item_sn == 'B' || $item_sn == 'C' || $item_sn == 'D' || $item_sn == 'H'){
						$hour = $e_hour;
					}elseif($item_sn == 'E' || $item_sn == 'G'){
						$hour = round($e_hour*0.8);
					}elseif($item_sn == 'F'){
						$hour = round($e_hour*1.5);
					}else{
						$hour = 0;
					}
					$total_price = round($hour*$unit_price,2);
					$sql_list = "INSERT INTO `db_mould_quote_list` (`quote_listid`,`quoteid`,`itemid`,`hour`,`unit_price`,`total_price`) VALUES (NULL,'$quoteid','$itemid','$hour','$unit_price','$total_price')";
					$db->query($sql_list);
					$sum_E_price += $total_price;
				}elseif($item_type_sn == 'F'){
					if($item_sn == 'C'){
						$total_price = round(($sum_A_price+$sum_B_price+$sum_C_price+$sum_D_price+$sum_E_price)*((float)$descripition/100),2);
						$total_price_FC = $total_price;
					}elseif($item_sn == 'D'){
						$total_price = round(($sum_A_price+$sum_B_price+$sum_C_price+$sum_D_price+$sum_E_price)*((float)$descripition/100),2);
						$total_price_FD = $total_price;
					}elseif($item_sn == 'E'){
						$total_price = round(($sum_A_price+$sum_B_price+$sum_C_price+$sum_D_price+$sum_E_price+$total_c_price+$total_d_price)*((float)$descripition/100),2);
						$total_price_FE = $total_price;
					}
					$sql_list = "INSERT INTO `db_mould_quote_list` (`quote_listid`,`quoteid`,`itemid`,`descripition`,`total_price`) VALUES (NULL,'$quoteid','$itemid','$descripition','$total_price')";
					$db->query($sql_list);
				}
			}
			//统计不含税
			$total_mould_price = round($sum_A_price+$sum_B_price+$sum_C_price+$sum_D_price+$sum_E_price+$total_price_FC+$total_price_FD,2);
			//统计美元
			$total_mould_price_usd = round($total_mould_price/1.02/6.5,2);
			//统计含税
			$total_mould_price_vat = round($total_mould_price+$total_price_FE,2);
			$sql_total = "UPDATE `db_mould_quote` SET `total_price` = '$total_mould_price',`total_price_usd` = '$total_mould_price_usd',`total_price_vat` = '$total_mould_price_vat' WHERE `quoteid` = '$quoteid'";
			$db->query($sql_total);
		}
		header("location:".$_SERVER['HTTP_REFERER']);
	}
}
?>