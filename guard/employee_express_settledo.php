<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
if($_POST['submit']){
	$express_num = str_replace_null(trim($_POST['express_num']));
	$apply_date = $_POST['apply_date'];
	$express_incid = $_POST['express_incid'];
	$consignee_inc = trim($_POST['consignee_inc']);
	$express_item = trim($_POST['express_item']);
	$paytype = $_POST['paytype'];
	$cost = $_POST['cost'];
	$express_status = $_POST['express_status'];
	$expressid = $_POST['expressid'];
	$reckoner = $_SESSION['employee_info']['employeeid'];
	$settle_time = fun_gettime();
	$sql = "UPDATE `db_employee_express` SET `express_num` = '$express_num',`apply_date` = '$apply_date',`express_incid` = '$express_incid',`consignee_inc` = '$consignee_inc',`express_item` = '$express_item',`paytype` = '$paytype',`cost` = '$cost',`express_status` = '$express_status',`reckoner` = '$reckoner',`settle_time` = '$settle_time' WHERE `expressid` = '$expressid'";
	$db->query($sql);
	if($db->affected_rows){
		header("location:".$_POST['pre_url']);
	}
}
?>