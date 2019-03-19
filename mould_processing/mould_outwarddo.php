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
	$supplierid = $_POST['supplierid'];
	$outward_typeid = $_POST['outward_typeid'];
	$cost = $_POST['cost'];
	$iscash = $_POST['iscash'];
	$applyer = trim($_POST['applyer']);
	$plan_date = $_POST['plan_date'];
	$actual_date = $_POST['actual_date'];
	$inout_status = $_POST['inout_status'];
	$remark = trim($_POST['remark']);
	$mouldid = $_POST['mouldid'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$sql = "INSERT INTO `db_mould_outward` (`outwardid`,`part_number`,`order_date`,`workteamid`,`order_number`,`quantity`,`supplierid`,`outward_typeid`,`cost`,`iscash`,`applyer`,`plan_date`,`actual_date`,`inout_status`,`outward_status`,`remark`,`mouldid`,`employeeid`,`dotime`) VALUES (NULL,'$part_number','$order_date','$workteamid','$order_number','$quantity','$supplierid','$outward_typeid','$cost','$iscash','$applyer','$plan_date','$actual_date','$inout_status',1,'$remark','$mouldid','$employeeid','$dotime')";
		$db->query($sql);
		if($db->insert_id){
			header("location:mould_outward.php");
		}
	}elseif($action == "edit"){
		$outwardid = $_POST['outwardid'];
		$outward_status = $_POST['outward_status'];
		$sql = "UPDATE `db_mould_outward` SET `part_number` = '$part_number',`order_date` = '$order_date',`workteamid` = '$workteamid',`order_number` = '$order_number',`quantity` = '$quantity',`supplierid` = '$supplierid',`outward_typeid` = '$outward_typeid',`cost` = '$cost',`iscash` = '$iscash',`applyer` = '$applyer',`plan_date` = '$plan_date',`actual_date` = '$actual_date',`inout_status` = '$inout_status',`outward_status` = '$outward_status',`remark` = '$remark' WHERE `outwardid` = '$outwardid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_id = $_POST['id'];
		$outwardid = fun_convert_checkbox($array_id);
		$sql_file = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkid` IN ($outwardid) AND `linkcode` = 'MO'";
		$result_file = $db->query($sql_file);
		if($result_file->num_rows){
			while($row_file = $result_file->fetch_assoc()){
				$filepath = "../upload/file/".$row_file['filedir'].'/'.$row_file['filename'];
				fun_delfile($filepath);
			}
		}
		$sql_file_list = "DELETE FROM `db_upload_file` WHERE `linkid` IN ($outwardid) AND `linkcode` = 'MO'";
		$db->query($sql_file_list);
		$sql = "DELETE FROM `db_mould_outward` WHERE `outwardid` IN ($outwardid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>