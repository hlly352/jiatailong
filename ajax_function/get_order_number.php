
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$supplierid = trim($_POST['supplierid']);
$order_date = trim($_POST['order_date']);
$account_type = trim($_POST['account_type']);
if($account_type == 'M'){
	$sql = "SELECT * FROM `db_material_order` WHERE `supplierid` = '$supplierid' AND `order_date` LIKE '$order_date%' AND `order_number` NOT IN(SELECT `order_number` FROM `db_funds_prepayment` WHERE `account_type` ='M')";
}elseif($account_type == 'C'){
	$sql = "SELECT * FROM `db_cutter_order` WHERE `supplierid` = '$supplierid' AND `order_date` LIKE '$order_date%' AND `order_number` NOT IN(SELECT `order_number` FROM `db_funds_prepayment` WHERE `account_type` ='C')";
}elseif($account_type == 'O'){
	$sql = "SELECT * FROM `db_other_material_order` WHERE `supplierid` = '$supplierid' AND `order_date` LIKE '$order_date%' AND `order_number` NOT IN(SELECT `order_number` FROM `db_funds_prepayment` WHERE `account_type` ='O')";
}
$result = $db->query($sql);
if($result->num_rows){
	$order_number = array();
	while($row = $result->fetch_assoc()){
		$order_number[] = $row;
	}
}
echo json_encode($order_number);
?>