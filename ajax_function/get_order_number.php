<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
$supplierid = trim($_POST['supplierid']);
$order_date = trim($_POST['order_date']);
$employeeid = $_POST['employeeid'];
$sql = "SELECT * FROM `db_material_order` WHERE `supplierid` = '$supplierid' AND `order_date` LIKE '$order_date%'";
$result = $db->query($sql);
if($result->num_rows){
	$order_number = array();
	while($row = $result->fetch_assoc()){
		$order_number[] = $row;
	}
}
echo json_encode($order_number);
?>