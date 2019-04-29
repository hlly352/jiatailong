<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';
$employee_id = $_SESSION['employee_info']['employeeid'];

if($_POST['submit']){
	$action = $_POST['action'];
	if($action == 'add' || $action == 'edit'){
		//接受客户信息
		$customer_info = $_POST;
		
	}
	if($action == 'add'){
		//拼接数据库语句
		unset($customer_info['submit']);
		unset($customer_info['action']);
		$customer_status_info = $customer_info;
		$status_arr = ['status_time','status_customer','status_contacts','status_boss','status_goal','status_result','status_plan','status_note'];
		//去除不添加到客户信息表的信息
		foreach($customer_info as $k=>$v){
			if(in_array($k,$status_arr)){
				unset($customer_info[$k]);
			}
		}
		//把数据是数组的转换为字符串
		foreach($customer_info as $key=>$value){
			if(is_array($value)){
				$customer_info[$key] = implode('$$',$value);
			} else {
			$customer_info[$key] = $value;
			}	
		}
		

		$key_word = ' ';
		$value_word = ' ';
		//拼接sql 语句的字段和值
		foreach($customer_info as $key => $value){
			$key_word .= '`'.$key.'`,';
			$value_word .= '"'.$value.'",';
		}

		$key_word = $key_word.'`add_time`,`adder_id`';
		$value_word = $value_word.time().','.$employee_id;
		$sql = "INSERT INTO `db_customer_info` ($key_word) VALUES($value_word)";
		//执行sql 语句
		$result = $db->query($sql);

		if($insert_id = $db->insert_id){
			//获取要添加到客户状态表中的数据
			foreach($customer_status_info as $k=>$v){
			if(!in_array($k,$status_arr)){
				unset($customer_status_info[$k]);
				}
			}	
		
			//拼接插入数据库的字段
			$sql_key = ' ';
			$sql_val = ' ';
			foreach($customer_status_info as $key=>$value){
				$sql_key .= '`'.$key.'`,';
				$sql_val .= '"'.$value.'",';

			}
			//加入客户id字段
			$sql_key .= '`customer_id`,`add_time`';
			$sql_val  .= $insert_id.','.time();
			//拼接sql语句
			$sql_status = "INSERT INTO `db_customer_status` ($sql_key) VALUES($sql_val)";
			$res = $db->query($sql_status);
			if($db->insert_id){
				header("location:customer_index.php");
			} else {
				header("location:customer_add.php");
			}
			
		}
	}elseif($action == 'edit'){
		//获取修改的数据
		$customer_data = $_POST;
		$customer_id = $_POST['customer_id'];
		unset($customer_data['customer_id']);
		unset($customer_data['submit']);
		unset($customer_data['action']);
		//遍历得到的数据,如果是数组则转换为祖符串
		$sql_str = ' ';
		foreach($customer_data as $key=>$value){
			if(is_array($value)){
				$sql_str .= '`'.$key.'` = "'.implode('$$',$value).'",';
				//$customer_data[$key] = implode('$$',$value);
			} else {
				//$customer_data[$key] = $value;
				$sql_str .= '`'.$key.'` = "'.$value.'",';
			}
		}
		//去掉末尾的逗号
		//$str = substr($str,0,strlen($str) -1);
		//拼接时间
		$sql_str .= $sql_str.'`add_time` = '.time();
		
		//拼接更新数据库的sql语句
		$sql = "UPDATE `db_customer_info` SET".$sql_str.' WHERE `customer_id` = '.$customer_id;
		$res = $db->query($sql);
		if($res){
			sleep(2);
			header("location:customer_index.php");
		}
		/*if($db->insert_id){
			header("location:mould_data.php");
		}
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}*/
	}elseif($action == 'del'){
		//接受要操作的id值
		$array_customer_dataid = fun_convert_checkbox($_POST['id']);
		
		/*$sql_list = "DELETE `db_mould_quote_list` FROM `db_mould_quote_list` INNER JOIN `db_mould_quote` ON `db_mould_quote`.`quoteid` = `db_mould_quote_list`.`quoteid` WHERE `db_mould_quote`.`mould_dataid` IN ($array_mould_dataid)";
		$db->query($sql_list);
		$sql_quote = "DELETE FROM `db_mould_quote` WHERE `mould_dataid` IN ($array_mould_dataid)";
		$db->query($sql_quote);
		$sql_image = "SELECT `image_filedir`,`image_filename` FROM `db_mould_data` WHERE `mould_dataid` IN ($array_mould_dataid)";
		$result_image = $db->query($sql_image);
		if($result_image->num_rows){
			while($row_image = $result_image->fetch_assoc()){
				$image_filedir = $row_image['image_filedir'];
				$image_filename = $row_image['image_filename'];
				$image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
				$image_big_filepath = "../upload/mould_image/".$image_filedir.'/B'.$image_filename;
				fun_delfile($image_filepath);
				fun_delfile($image_big_filepath);
			}
		}
		$sql = "DELETE FROM `db_mould_data` WHERE `mould_dataid` IN ($array_mould_dataid)";*/
		//拼写删除sql 语句
		$sql = "DELETE FROM `db_customer_info` WHERE `customer_id` IN ($array_customer_dataid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	} 
}
?>