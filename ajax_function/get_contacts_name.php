<?php
	require_once '../global_mysql_connect.php';

	$customer_id = $_POST['customer_id'];
	$contacts_name = $_POST['contacts_name'];
	//根据选择的客户id值查询客户的详细信息
	$sql = "SELECT `contacts_tel`,`contacts_email` FROM `db_customer_info` WHERE `customer_id` = '$customer_id'";
	$result = $db->query($sql);
	$customer_info = [] ;
	while($row = $result->fetch_assoc()){
		//判断查询的客户是否有多个联系人
		if(strstr($row['contacts_name'],'$$')){
			$customer_info[] = explode('$$',$row['contacts_name']);
			$customer_info[] = explode('$$',$row['contacts_tel']);
			$customer_info[] = explode('$$',$row['contacts_email']);
		} else {
	
			$customer_info[] = $row;	
		}
		
	}

	echo json_encode($customer_info);
	
?>