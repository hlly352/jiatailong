<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	if($action == "add" || $action == "edit"){
		$purchaseid = $_POST['purchaseid'];
		$specificationid = $_POST['specificationid'];
		$hardnessid = $_POST['hardnessid'];
		$brandid = $_POST['brandid'];
		$supplierid = $_POST['supplierid'];
		$quantity = $_POST['quantity'];
		$plan_date = $_POST['plan_date'];
		$remark = trim($_POST['remark']);
	}
	if($action == "add"){
		$sql_cutter = "SELECT `cutterid` FROM `db_mould_cutter` WHERE `specificationid` = '$specificationid' AND `hardnessid` = '$hardnessid'";
		$result_cutter = $db->query($sql_cutter);
		if($result_cutter->num_rows){
			$array_cutter = $result_cutter->fetch_assoc();
			$cutterid = $array_cutter['cutterid'];
		}else{
			$sql = "INSERT INTO `db_mould_cutter` (`cutterid`,`specificationid`,`hardnessid`) VALUES (NULL,'$specificationid','$hardnessid')";
			$db->query($sql);
			$cutterid = $db->insert_id;
		}
		if($cutterid){
			$sql_list = "INSERT INTO `db_cutter_purchase_list` (`purchase_listid`,`purchaseid`,`cutterid`,`brandid`,`supplierid`,`quantity`,`plan_date`,`remark`) VALUES (NULL,'$purchaseid','$cutterid','$brandid','$supplierid','$quantity','$plan_date','$remark')";
			$db->query($sql_list);
			if($db->insert_id){
				header("location:cutter_purchase_list_info.php?purchaseid=".$purchaseid);
			}
		}
	}elseif($action == "edit"){
		$purchase_listid = $_POST['purchase_listid'];
		$sql_cutter = "SELECT `cutterid` FROM `db_mould_cutter` WHERE `specificationid` = '$specificationid' AND `hardnessid` = '$hardnessid'";
		$result_cutter = $db->query($sql_cutter);
		if($result_cutter->num_rows){
			$array_cutter = $result_cutter->fetch_assoc();	
			$sql_list  = "UPDATE `db_cutter_purchase_list` SET `brandid` = '$brandid',`supplierid` = '$supplierid',`quantity` = '$quantity',`plan_date` = '$plan_date',`remark` = '$remark' WHERE `purchase_listid` = '$purchase_listid'";
		}else{
			$sql = "INSERT INTO `db_mould_cutter` (`cutterid`,`specificationid`,`hardnessid`) VALUES (NULL,'$specificationid','$hardnessid')";
			$db->query($sql);
			if($cutterid = $db->insert_id){
				$sql_list = "UPDATE `db_cutter_purchase_list` SET `cutterid` = '$cutterid',`brandid` = '$brandid',`quantity` = '$quantity',`plan_date` = '$plan_date',`remark` = '$remark' WHERE `purchase_listid` = '$purchase_listid'";
			}
		}
		$db->query($sql_list );
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "del"){
		$array_purchase_listid = fun_convert_checkbox($_POST['id']);
		$sql = "DELETE FROM `db_cutter_purchase_list` WHERE `purchase_listid` IN ($array_purchase_listid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
}
?>