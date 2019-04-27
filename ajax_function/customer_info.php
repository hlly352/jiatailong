<?php
	require_once '../global_mysql_connect.php';
	$contacts_name = $_POST['sel_name'];
	
		$customer_id = $_POST['customer_id'];
		//根据选择的客户id值查询客户的详细信息
		$sql = "SELECT * FROM `db_customer_info` WHERE `customer_id` = '$customer_id'";
		$result = $db->query($sql);
		
	if(empty($contacts_name)){
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
	} else {
		$contacts_info = [];

		$sel_name = [];
		while($row = $result->fetch_assoc()){
			$sel_name = explode('$$',$row['contacts_name']);
			//找出所选联系人对应的键名
			foreach($sel_name as $k=>$val){
				if($sel_name[$k] == $contacts_name){
					$key = $k;
				}
			}

			$contacts_info[] = explode('$$',$row['contacts_tel'])[$key];
			$contacts_info[] = explode('$$',$row['contacts_email'])[$key];
			
 		}
 	
 		echo json_encode($contacts_info);
	}
	
?>