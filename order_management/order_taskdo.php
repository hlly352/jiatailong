<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';
$employeeid = $_SESSION['employee_info']['employeeid'];

$action = $_GET['action'];
//执行添加操作
if($action == 'add'){
	$data = $_POST;
	//去除客户代码
	$customer_code = $data['customer_code'];
	unset($data['customer_code']);
	//遍历得到的结果
	$sql_key = ' ';
	
	$arr = [];

	foreach($data as $key=>$value){
		$sql_key .= '`'.$key.'`,';
		foreach($value as $k=>$v){
			$sql_value .= '"'.$v.'",';
			$arr[$k][] = $v;
			
		}
		//$sql_value .= '"'.$employeeid.'","'.time().'","1","1"';
	 	
	//$task_sql = "INSERT INTO `db_mould_data`($sql_key) VALUES($sql_value)";
	
	}
	 $sql_key .= '`employeeid`,`deal_time`,`is_approval`,`is_deal`';
	 //拼接插入的值
	 $sql_value = ' ';
	foreach($arr as $k=>$v){
		$sql_value .='(';
		foreach($v as $key=>$value){
			$sql_value .= '"'.$value.'",';
		}
		
		$sql_value .= '"'.$employeeid.'","'.time().'","1","1"),';
		
		
	}
	//去除最后一个逗号
	$sql_value = substr($sql_value,0,strlen($sql_value)-1);
	 $task_sql = "INSERT INTO `db_mould_data`($sql_key) VALUES".$sql_value;	

	/*//拼接插入的sql 语句
	$sql_key = ' ';
	$sql_value = ' ';
	foreach($data as $key=>$value){
		$sql_key .= '`'.$key.'`,';
		$sql_value .= '"'.$value.'",';
	}
	 $sql_key .= '`employeeid`,`deal_time`,`is_approval`,`is_deal`';
	 $sql_value .= '"'.$employeeid.'","'.time().'","1","1"';
	$task_sql = "INSERT INTO `db_mould_data`($sql_key) VALUES($sql_value)";
	*/
	$result = $db->query($task_sql);
	$i = 0;
	if($db->affected_rows){
		//插入客户代码
		foreach($customer_code as $k=>$v){
			//sql语句
			$customer_sql = "UPDATE `db_customer_info` SET `customer_code`=\"{$v}\" WHERE `customer_id`=".$data['client_name'][$k];
			$db->query($customer_sql);
			if(!$db->affected_rows){
				$i++;
			}
		}
		//判断sql语句是否执行成功
		if($i == 0){
			header('location:order_approval.php');
		} else {
			header('location:order_approval.php');
			}
		}

	}elseif($action == 'order_approval_edit'){
		//获取id
		$data = $_POST;
		$mould_id = $data['mould_id'];
		unset($data['mould_id']);
		//更改订单信息
		$sql_value = ' ';
		foreach($data as $k=>$v){
			$sql_value .= '`'.$k.'`="'.$v.'",';
		}
		$sql_value = substr($sql_value,0,strlen($sql_value)-1);
		//拼接sql 语句
		$sql = "UPDATE `db_mould_data` SET {$sql_value} WHERE `mould_dataid` =".$mould_id;
		echo $sql;exit;
		$db->query($sql);
		if($db->affected_rows){
			header('location:order_approval_show.php?mould_id='.$mould_id);
		} else {
			header('location:order_approval_show.php?mould_id='.$mould_id);
		}
	}elseif($action == 'order_approval'){
		//获取id
		$mould_id = $_GET['mould_id'];
		//审批订单
		$sql = "UPDATE `db_mould_data` SET `order_approval`='1',`order_approval_time`=".time()." WHERE `mould_dataid`=".$mould_id;
		$db->query($sql);
		if($db->affected_rows){
			header('location:order_gather.php');
		}else{
			header('location:order_approval.php');
		}
	}


?>