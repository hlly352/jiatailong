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
	} else {
		header('location:order_gather.php');
		}
	} elseif($action == 'edit'){
		$data = $_POST;

		//把数组转换为字符串
		foreach($data as $k=>$v){
			if(is_array($v)){
				$data[$k] = implode('$$',$v);
			} else {
				$data[$k] = $v;
			}
		}
		//获取项目的id值
		$id = $data['specification_id'];
		unset($data['specification_id']);
		//拼接sql语句
		$sql_word = '';
		foreach($data as $k=>$v){
			$sql_word .='`'.$k.'`="'.$v.'",';
		}
		//更新时间
		$sql_word .= '`specification_time`="'.time().'"';

		$sql = "UPDATE `db_mould_specification` SET $sql_word WHERE `mould_specification_id`=".$id;
		$db->query($sql);
		if($db->affected_rows){
			header('location:../project_management/new_project.php');
		}

	}

?>