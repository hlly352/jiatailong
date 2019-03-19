<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	if($action == "edit"){
	$tryid = $_POST['tryid'];
	$supplierid = $_POST['supplierid'];
	$order_number = trim($_POST['order_number']);
	$try_date = $_POST['try_date'];
	$unit_price = $_POST['unit_price'];
	$cost = $_POST['cost'];
	$mould_size = trim($_POST['mould_size']);
	$molding_cycle = $_POST['molding_cycle'];
	$plan_date = $_POST['plan_date'];
	$try_times = $_POST['try_times'];
	$try_causeid = $_POST['try_causeid'];
	$tonnage = $_POST['tonnage'];
	$plastic_material_color = trim($_POST['plastic_material_color']);
	$plastic_material_offer = trim($_POST['plastic_material_offer']);
	$product_weight = $_POST['product_weight'];
	$product_quantity = $_POST['product_quantity'];
	$material_weight = $_POST['material_weight'];
	$try_status = $_POST['try_status'];
	$remark = trim($_POST['remark']);
	$sql = "UPDATE `db_mould_try` SET `mould_size` = '$mould_size',`molding_cycle` = '$molding_cycle',`plan_date` = '$plan_date',`try_times` = '$try_times',`try_causeid` = '$try_causeid',`tonnage` = '$tonnage',`plastic_material_color` = '$plastic_material_color',`plastic_material_offer` = '$plastic_material_offer',`product_weight` = '$product_weight',`product_quantity` = '$product_quantity',`material_weight` = '$material_weight',`supplierid` = '$supplierid',`order_number` = '$order_number',`try_date` = '$try_date',`unit_price` = '$unit_price',`cost` = '$unit_price',`try_status` = '$try_status',`remark` = '$remark' WHERE `tryid` = '$tryid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_POST['pre_url']);
	}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$tryid = fun_convert_checkbox($array_id);
		$sql_file = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkid` IN ($tryid) AND `linkcode` = 'MT'";
		$result_file = $db->query($sql_file);
		if($result_file->num_rows){
			while($row_file = $result_file->fetch_assoc()){
				$filepath = "../upload/file/".$row_file['filedir'].'/'.$row_file['filename'];
				fun_delfile($filepath);
			}
		}
		$sql_file_list = "DELETE FROM `db_upload_file` WHERE `linkid` IN ($tryid) AND `linkcode` = 'MT'";
		$db->query($sql_file_list);
		$sql_approve = "DELETE FROM `db_office_approve` WHERE `linkid` IN ($tryid) AND `approve_type` = 'MT'";
		$db->query($sql_approve);
		$sql = "DELETE FROM `db_mould_try` WHERE `tryid` IN ($tryid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
		
	}
}
?>