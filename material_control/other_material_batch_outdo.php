<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$array_specificationid = $_POST['specificationid'];
	$array_dodate = $_POST['dodate'];
	$array_form_number = $_POST['form_number'];
	$array_inout_quantity = $_POST['inout_quantity'];
	$array_taker = $_POST['taker'];
	$array_remark = $_POST['remark'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	foreach($array_specificationid as $key=>$specificationid){
		$dodate = $array_dodate[$key];
		$form_number = trim($array_form_number[$key]);
		$inout_quantity = $array_inout_quantity[$key];
		$taker = trim($array_taker[$key]);
		$remark = trim($array_remark[$key]);
		if($taker){
			//减去库存
				$sql_update = "UPDATE `db_other_material_specification` SET `stock` = `stock` - '$inout_quantity' WHERE `specificationid` = '$specificationid'";
				$db->query($sql_update);
				$sql_add .= "(NULL,'$dodate','O','$form_number','$inout_quantity','$taker','$remark','$specificationid`','$employeeid','$dotime'),";
			
		}
	}
	$sql_add = rtrim($sql_add,',');
	$sql = "INSERT INTO `db_other_material_inout` (`inoutid`,`dodate`,`dotype`,`form_number`,`inout_quantity`,`taker`,`remark`,`listid`,`employeeid`,`dotime`) VALUES $sql_add";
	$db->query($sql);
	if($db->insert_id){
		header("location:other_inout_list_out.php");
	}
}
?>