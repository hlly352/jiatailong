<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$material_date = fun_getdate();
	$material_list_number = trim($_POST['material_list_number']);
	$material_list_sn = trim($_POST['material_list_sn']);
	$material_number = trim($_POST['material_number']);
	$material_name = trim($_POST['material_name']);
	$specification = str_replace_array(trim($_POST['specification']));
	$material_quantity = $_POST['material_quantity'];
	$texture = trim($_POST['texture']);
	$hardness = trim($_POST['hardness']);
	$brand = trim($_POST['brand']);
	$spare_quantity = $_POST['spare_quantity'];
	$remark = str_replace_array(trim($_POST['remark']));
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$mouldid = $_POST['mouldid'];
		$sql = "INSERT INTO `db_mould_material` (`materialid`,`mouldid`,`material_date`,`material_list_number`,`material_list_sn`,`material_number`,`material_name`,`specification`,`material_quantity`,`texture`,`hardness`,`brand`,`spare_quantity`,`remark`,`complete_status`,`employeeid`,`dotime`) VALUES (NULL,'$mouldid','$material_date','$material_list_number','$material_list_sn','$material_number','$material_name','$specification','$material_quantity','$texture','$hardness','$brand','$spare_quantity','$remark',1,'$employeeid','$dotime')";
		$db->query($sql);
		if($db->insert_id){
			header("location:mould_material_list.php");
		}
	}elseif($action == "edit"){
		$materialid = $_POST['materialid'];
		$sql = "UPDATE `db_mould_material` SET `material_date` = '$material_date',`material_list_number` = '$material_list_number',`material_list_sn` = '$material_list_sn',`material_number` = '$material_number',`material_name` = '$material_name',`specification` = '$specification',`material_quantity` = '$material_quantity',`texture` = '$texture',`hardness` = '$hardness',`brand` = '$brand',`spare_quantity` = '$spare_quantity',`remark` = '$remark',`complete_status` = 1 WHERE `materialid` = '$materialid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$materialid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_mould_material` WHERE `materialid` IN ($materialid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>