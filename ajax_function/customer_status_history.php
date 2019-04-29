<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
//获取客户状态id
$customer_id = trim($_POST['customer_id']);

//查询历史状态
$sql  = "SELECT * FROM `db_customer_status` WHERE `customer_id` = ".$customer_id.' ORDER  BY `add_time` DESC';
$result = $db->query($sql);

if($status_count = $result->num_rows){
	$res = [];
	while($row = $result->fetch_assoc()){
		$row['count'] = $status_count;
		$res[] = $row; 
	}
	echo json_encode($res);
}

?>