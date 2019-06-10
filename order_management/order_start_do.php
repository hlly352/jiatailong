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
				foreach($data as $key=>$value){
				if(is_array($value)){
					$value = implode('$$',$value);
				}
				$sql_key .= '`'.$key.'`,';
				$sql_val .= '"'.$value.'",';
					
				}
				$sql_val .= '"'.$employeeid.'","'.time().'"';
				$sql_key .= '`employeeid`,`specification_time`';
	
		
		
	}
	//去除最后一个逗号
	$sql_value = substr($sql_value,0,strlen($sql_value)-1);
	 $specification_sql = "INSERT INTO `db_mould_specification`($sql_key) VALUES(".$sql_val.")";
	 $mould_data_sql = "UPDATE `db_mould_data` SET `is_start`='1' WHERE `mould_dataid`={$data['mould_id']}";
	//执行sql语句
	$db->query($specification_sql);
	if($db->affected_rows){
		$db->query($mould_data_sql);
		if($db->affected_rows){
			header('location:order_gather.php');
		} else {
			header('location:order_gather.php');
		}
	}

?>