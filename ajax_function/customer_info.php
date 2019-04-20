<?php
	require_once '../global_mysql_connect.php';

	$customer_name = $_POST['customer_name'];
	$sql = "SELECT * FROM `db_customer_info` WHERE `customer_name` = '$customer_name'";
	$result = $db->query($sql);

	while($row = $result->fetch_assoc()){
		$customer_info[] = $row;
	}

	echo json_encode($customer_info);
	
?>