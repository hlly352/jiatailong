<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$part_number = trim($_POST['part_number']);
	$order_date = $_POST['order_date'];
	$workteamid = $_POST['workteamid'];
	$order_number = trim($_POST['order_number']);
	$quantity = $_POST['quantity'];
	$weld_cause = trim($_POST['weld_cause']);
	$teamid = trim($_POST['teamid']);
	$supplierid = $_POST['supplierid'];
	$weld_typeid = $_POST['weld_typeid'];
	$cost = $_POST['cost'];
	$applyer = trim($_POST['applyer']);
	$plan_date = $_POST['plan_date'];
	$actual_date = $_POST['actual_date'];
	$inout_status = $_POST['inout_status'];
	$remark = trim($_POST['remark']);
	$mouldid = $_POST['mouldid'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$sql = "INSERT INTO `db_mould_weld` (`weldid`,`part_number`,`order_date`,`workteamid`,`order_number`,`quantity`,`weld_cause`,`teamid`,`supplierid`,`weld_typeid`,`cost`,`applyer`,`plan_date`,`actual_date`,`inout_status`,`weld_status`,`remark`,`mouldid`,`employeeid`,`dotime`) VALUES (NULL,'$part_number','$order_date','$workteamid','$order_number','$quantity','$weld_cause','$teamid','$supplierid','$weld_typeid','$cost','$applyer','$plan_date','$actual_date','$inout_status',1,'$remark','$mouldid','$employeeid','$dotime')";
		$db->query($sql);
		if($db->insert_id){
			header("location:mould_weld.php");
		}
	}elseif($action == "edit"){
		$weldid = $_POST['weldid'];
		$weld_status = $_POST['weld_status'];
		$sql = "UPDATE `db_mould_weld` SET `part_number` = '$part_number',`order_date` = '$order_date',`workteamid` = '$workteamid',`order_number` = '$order_number',`quantity` = '$quantity',`weld_cause` = '$weld_cause',`teamid` = '$teamid',`supplierid` = '$supplierid',`weld_typeid` = '$weld_typeid',`cost` = '$cost',`applyer` = '$applyer',`plan_date` = '$plan_date',`actual_date` = '$actual_date',`inout_status` = '$inout_status',`weld_status` = '$weld_status',`remark` = '$remark' WHERE `weldid` = '$weldid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$weldid = fun_convert_checkbox($array_id);
		$sql_file = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkid` IN ($weldid) AND `linkcode` = 'MW'";
		$result_file = $db->query($sql_file);
		if($result_file->num_rows){
			while($row_file = $result_file->fetch_assoc()){
				$filepath = "../upload/file/".$row_file['filedir'].'/'.$row_file['filename'];
				fun_delfile($filepath);
			}
		}
		$sql_file_list = "DELETE FROM `db_upload_file` WHERE `linkid` IN ($weldid) AND `linkcode` = 'MW'";
		$db->query($sql_file_list);
		$sql = "DELETE FROM `db_mould_weld` WHERE `weldid` IN ($weldid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>