<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once 'shell.php';
$diff = trim($_GET['diff']);
$inoutid = trim($_GET['inoutid']);

//更改核销数量
$cancel_num_sql = "UPDATE `db_cutter_inout` SET `cancel_num` = '$diff' WHERE `inoutid` = '$inoutid'";
$db->query($cancel_num_sql);
//查找物料的核销金额
$cancel_amount_sql = "SELECT (`db_cutter_order_list`.`unit_price` * `db_cutter_inout`.`cancel_num`) AS `cancel_amount` FROM `db_cutter_inout` INNER JOIN `db_cutter_order_list` ON `db_cutter_inout`.`listid` = `db_cutter_order_list`.`listid` WHERE `db_cutter_inout`.`inoutid` = '$inoutid'";

$result_cancel_amount = $db->query($cancel_amount_sql);
if($result_cancel_amount->num_rows){
	$cancel_amount = $result_cancel_amount->fetch_row()[0];
}

//更改核销金额
$cancel_sql = "UPDATE `db_cutter_inout` SET `cancel_amount` = '$cancel_amount' WHERE `inoutid` = '$inoutid'";
$db->query($cancel_sql);

	header('location:'.$_SERVER['HTTP_REFERER']);



?>