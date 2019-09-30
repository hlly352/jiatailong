<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_listid = $_POST['listid'];
	$array_dodate = $_POST['dodate'];
	$array_form_number = $_POST['form_number'];
	$array_quantity = $_POST['quantity'];
	$array_inout_quantity = $_POST['inout_quantity'];
	$array_amount = $_POST['amount'];
	$array_remark = $_POST['remark'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	foreach($array_listid as $key=>$listid){
		$dodate = $array_dodate[$key];
		$form_number = trim($array_form_number[$key]);
		$quantity = $array_quantity[$key];
		$inout_quantity = $array_inout_quantity[$key];
		$amount = $array_amount[$key];
		$remark = trim($array_remark[$key]);
		if($form_number){
			$sql_list = "SELECT * FROM `db_other_material_orderlist` WHERE `listid` = '$listid' AND (`actual_quantity`-`in_quantity`) >= $quantity";
			$result_list = $db->query($sql_list);
			if($result_list->num_rows){
				$sql_add .= "(NULL,'$dodate','I','$form_number','$quantity','$inout_quantity','$amount','$remark','$listid`','$employeeid','$dotime'),";
				//入库数据插入到订单列表表中
				$sql_update = "UPDATE `db_other_material_orderlist` SET `in_quantity` = `in_quantity` + '$quantity' WHERE `listid` = '$listid'";
				// echo $sql_update;exit;
				 $db->query($sql_update);
				//查找规格id
				$sql_id = "SELECT `db_other_material_specification`.`specificationid`,`db_mould_other_material`.`mould_other_id` FROM `db_other_material_orderlist` INNER JOIN `db_mould_other_material` ON `db_other_material_orderlist`.`materialid` = `db_mould_other_material`.`mould_other_id` INNER JOIN `db_other_material_specification` ON `db_mould_other_material`.`material_name` = `db_other_material_specification`.`specificationid`  WHERE `db_other_material_orderlist`.`listid` = '$listid'";
				$result_id = $db->query($sql_id);
				if($result_id->num_rows){
					while($infos = $result_id->fetch_row()){
		    			//入库数据插入到期间物料规格表中
						$sql_specification = "UPDATE `db_other_material_specification` SET `stock` = `stock` + '$quantity' WHERE `specificationid` = '$infos[0]'";
						$db->query($sql_specification);
						//更改物料的状态
						$sql_mould_other = "UPDATE `db_mould_other_material` SET `status` = 'G' WHERE `mould_other_id` = '$infos[1]'";
						$db->query($sql_mould_other);
					}
				}	
			}
		}
	}
	$sql_add = rtrim($sql_add,',');
	$sql = "INSERT INTO `db_other_material_inout` (`inoutid`,`dodate`,`dotype`,`form_number`,`quantity`,`inout_quantity`,`amounts`,`remark`,`listid`,`employeeid`,`dotime`) VALUES $sql_add";
	$db->query($sql);
	if($db->insert_id){
		header("location:other_inout_list_in.php");
	}
}
?>