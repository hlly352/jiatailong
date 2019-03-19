<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
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
	$approver = $_POST['approver'];
	$remark = trim($_POST['remark']);
	$mouldid = $_POST['mouldid'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$sql = "INSERT INTO `db_mould_try` (`tryid`,`mould_size`,`molding_cycle`,`plan_date`,`try_times`,`try_causeid`,`tonnage`,`plastic_material_color`,`plastic_material_offer`,`product_weight`,`product_quantity`,`material_weight`,`remark`,`try_status`,`approver`,`approve_status`,`mouldid`,`employeeid`,`dotime`) VALUES (NULL,'$mould_size','$molding_cycle','$plan_date','$try_times','$try_causeid','$tonnage','$plastic_material_color','$plastic_material_offer','$product_weight','$product_quantity','$material_weight','$remark',1,'$approver','A','$mouldid','$employeeid','$dotime')";
		$db->query($sql);
		if($tryid = $db->insert_id){
			header("location:mould_try_applyae.php?action=add");
		}
	}elseif($action == "edit"){
		$tryid = $_POST['tryid'];
		$sql = "UPDATE `db_mould_try` SET `mould_size` = '$mould_size',`molding_cycle` = '$molding_cycle',`plan_date` = '$plan_date',`try_times` = '$try_times',`try_causeid` = '$try_causeid',`tonnage` = '$tonnage',`plastic_material_color` = '$plastic_material_color',`plastic_material_offer` = '$plastic_material_offer',`product_weight` = '$product_weight',`product_quantity` = '$product_quantity',`material_weight` = '$material_weight',`approver` = '$approver',`approve_status` = 'A',`remark` = '$remark' WHERE `tryid` = '$tryid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$tryid = fun_convert_checkbox($array_id);
		$sql = "DELETE FROM `db_mould_try` WHERE `tryid` IN ($tryid)";
		$db->query($sql);
		$sql_approve = "DELETE FROM `db_office_approve` WHERE `linkid` IN ($tryid) AND `approve_type` = 'MT'";
		$db->query($sql_approve);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
	if($action == "add" || $action == "edit"){
		$sql_mould_try = "SELECT `db_mould`.`mould_number`,`db_employee`.`employee_name`,`db_employee`.`email` FROM `db_mould_try` INNER JOIN `db_employee` ON `db_employee`.`employeeid` = `db_mould_try`.`employeeid` INNER JOIN `db_mould` ON `db_mould`.`mouldid` = `db_mould_try`.`mouldid` WHERE `db_mould_try`.`tryid` = '$tryid'";
		$result_mould_try = $db->query($sql_mould_try);
		if($result_mould_try->num_rows){
			$array_mould_try = $result_mould_try->fetch_assoc();
			$mould_number = $array_mould_try['mould_number'];
			$employee_name = $array_mould_try['employee_name'];
			$email_name = $array_mould_try['email'];
			$email_subject = $mould_number."试模申请到达";
			$email_content = $employee_name."申请的".$mould_number."试模到达，请及时审批。";
			$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
			$db->query($sql_email);
		}
	}
}
?>