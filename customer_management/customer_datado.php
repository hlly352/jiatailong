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
	if($action == 'add' || $action == 'edit' || $action=='status_edit' || $action == 'customer_edit'){
		//接受客户信息
		$customer_info = $_POST;
		
	}
	if($action == 'add'){
		$adder = trim($customer_info['min_boss'][0]);
			//把负责人换为最新的
			$add_sql = "SELECT `employeeid` FROM `db_employee` WHERE `employee_name` LIKE '%".$adder."%'";
			
			$add_res = $db->query($add_sql);
			if($add_res->num_rows){
				$adder_id = $add_res->fetch_row()[0];
			}
		//拼接数据库语句
		unset($customer_info['submit']);
		unset($customer_info['action']);
		$customer_status_info = $customer_info;
		$status_arr = ['status_time','status_customer','status_grade','status_phone','status_code','status_contacts','status_boss','status_goal','status_result','status_plan','status_note'];
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
		$microtime = microtime(true) * 10000;
		$key_word = $key_word.'`add_times`,`adder_id`,`add_date`';
		$value_word = $value_word.$microtime.','.$adder_id.','.time();
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
			$microtime = microtime(true) * 10000;
			$sql_key .= '`customer_id`,`add_time`,`employee_id`';
			$sql_val  .= $insert_id.','.$microtime.','.$adder_id;
			//拼接sql语句
			$sql_status = "INSERT INTO `db_customer_status` ($sql_key) VALUES($sql_val)";
			$res = $db->query($sql_status);
			if($db->insert_id){
				header("location:customer_status.php");
			} else {
				header("location:customer_add.php");
			}
			
		}
	}elseif($action == 'edit'){
		unset($customer_info['submit']);
			unset($customer_info['action']);
			$customer_id = $customer_info['customer_id'];
			unset($customer_info['customer_id']);
			$customer_status_info = $customer_info;
			$status_arr = ['status_time','status_customer','status_grade','status_phone','status_code','status_contacts','status_boss','status_goal','status_result','status_plan','status_note'];
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
			//拼接数据库字段
			$sql_word = ' ';
			foreach($customer_info as $k=>$v){
				$sql_word .= '`'.$k.'`="'.$v.'",';
			}
			//拼接sql 语句
			$sql_word .= '`adder_id`='.$employee_id.',`add_times` = '.time(); 
			$customer_sql = "UPDATE `db_customer_info` SET".$sql_word." WHERE `customer_id` =".$customer_id;
			$result = $db->query($customer_sql);
			if(isset($customer_status_info['status_customer'])){
				if($db->affected_rows){
					
					//获取要添加到客户状态表中的数据
					foreach($customer_status_info as $k=>$v){
					if(!in_array($k,$status_arr)){
						unset($customer_status_info[$k]);
						}
					}
					
					$sql_val = [];
					foreach($customer_status_info as $k=>$v){
						$sql_key .= '`'.$k.'`,';
						foreach($v as $key=>$val){
							$sql_val[$key][] = $val;
						}

					}
					$sql_key .= '`add_time`,`employee_id`,`customer_id`';
					//拼接sql语句
					$i = 0;
					
					foreach($sql_val as $ks=>$vs){
						$val ='"'.implode('","',$vs);
						$microtime = microtime(true) * 10000;
						$val .= '",'.$microtime.','.$employee_id.','.$customer_id;

						//循环插入数据
						$sql = "INSERT INTO `db_customer_status`($sql_key) VALUES($val)";
						
						$res = $db->query($sql);
						if($db->affected_rows != 1){
							$i +=1;
							
						}
						sleep(1);
					}
					//判断时候都插入成功
					//echo $i;
					if($i == 0){
						header('location:customer_index.php');
					}
						
				}
			}else{
				header('location:customer_index.php');
			}
		}elseif($action == 'customer_edit'){
			$adder = trim($customer_info['min_boss'][0]);
			//把负责人换为最新的
			$add_sql = "SELECT `employeeid` FROM `db_employee` WHERE `employee_name` LIKE '%".$adder."%'";
			
			$add_res = $db->query($add_sql);
			if($add_res->num_rows){
				$adder_id = $add_res->fetch_row()[0];
			}
			unset($customer_info['submit']);
			unset($customer_info['action']);
			$customer_id = $customer_info['customer_id'];
			unset($customer_info['customer_id']);
			//把数据是数组的转换为字符串
			foreach($customer_info as $key=>$value){
				if(is_array($value)){
					$customer_info[$key] = implode('$$',$value);
				} else {
				$customer_info[$key] = $value;
				}	
			}
			//拼接数据库字段
			$sql_word = ' ';
			foreach($customer_info as $k=>$v){
				$sql_word .= '`'.$k.'`="'.$v.'",';
			}
			//拼接sql 语句
			$sql_word .= '`adder_id`='.$employee_id.',`add_times` = '.time().',`adder_id`='.$adder_id; 
			$customer_sql = "UPDATE `db_customer_info` SET".$sql_word." WHERE `customer_id` =".$customer_id;

			$result = $db->query($customer_sql);
			if($db->affected_rows){
				//获取要添加到客户状态表中的数据
				foreach($customer_status_info as $k=>$v){
				if(!in_array($k,$status_arr)){
					unset($customer_status_info[$k]);
					}
				}
				
				$sql_val = [];
				foreach($customer_status_info as $k=>$v){
					$sql_key .= '`'.$k.'`,';
					foreach($v as $key=>$val){
						$sql_val[$key][] = $val;
					}

				}
				$sql_key .= '`add_time`,`employee_id`,`customer_id`';
				//拼接sql语句
				$i = 0;
				
				foreach($sql_val as $ks=>$vs){
					$val ='"'.implode('","',$vs);
					$microtime = microtime(true) * 10000;
					$val .= '",'.$microtime.','.$employee_id.','.$customer_id;

					//循环插入数据
					$sql = "INSERT INTO `db_customer_status`($sql_key) VALUES($val)";
					
					$res = $db->query($sql);
					if($db->affected_rows != 1){
						$i +=1;
						
					}
					sleep(1);
				}
				//判断时候都插入成功
				//echo $i;
				if($i == 0){
					header('location:customer_index.php');
				}
			}
			
		}elseif($action == 'status_edit'){
			
			$adder = trim($customer_info['min_boss'][0]);
			//把负责人换为最新的
			$add_sql = "SELECT `employeeid` FROM `db_employee` WHERE `employee_name` LIKE '%".$adder."%'";
		
			$add_res = $db->query($add_sql);
			if($add_res->num_rows){
				$adder_id = $add_res->fetch_row()[0];
			}
			
			unset($customer_info['submit']);
			unset($customer_info['action']);
			$customer_id = $customer_info['customer_id'];
			unset($customer_info['customer_id']);
			
			$customer_status_info = $customer_info;
			$status_arr = ['status_time','status_customer','status_grade','status_phone','status_code','status_contacts','status_boss','status_goal','status_result','status_plan','status_note'];
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
			//拼接数据库字段
			$sql_word = ' ';
			foreach($customer_info as $k=>$v){
				$sql_word .= '`'.$k.'`="'.$v.'",';
			}
			//拼接sql 语句
			
			$sql_word .= '`adder_id`='.$adder_id.',`add_times` = '.time(); 
			$customer_sql = "UPDATE `db_customer_info` SET".$sql_word." WHERE `customer_id` =".$customer_id;
			$result = $db->query($customer_sql);
		
			if($db->affected_rows){
				
				//获取要添加到客户状态表中的数据
				foreach($customer_status_info as $k=>$v){
				if(!in_array($k,$status_arr)){
					unset($customer_status_info[$k]);
					}
				}
				
				$sql_val = [];
				foreach($customer_status_info as $k=>$v){
					$sql_key .= '`'.$k.'`,';
					foreach($v as $key=>$val){
						$sql_val[$key][] = $val;
					}

				}
				$sql_key .= '`add_time`,`employee_id`,`customer_id`';
				//拼接sql语句
				$i = 0;
				
				foreach($sql_val as $ks=>$vs){
					$val ='"'.implode('","',$vs);
					$microtime = microtime(true) * 10000;
					$val .= '",'.$microtime.','.$employee_id.','.$customer_id;

					//循环插入数据
					$sql = "INSERT INTO `db_customer_status`($sql_key) VALUES($val)";
					
					$res = $db->query($sql);
					if($db->affected_rows != 1){
						$i +=1;
						
					}
					sleep(1);
				}
				//判断时候都插入成功
				//echo $i;
				if($i == 0){
					header('location:customer_status.php');
				}
					
			}

		}elseif($action == 'status_del'){
			//接受要操作的id值
			$array_customer_dataid = fun_convert_checkbox($_POST['id']);
			//拼写删除sql 语句
			$sql = "DELETE FROM `db_customer_status` WHERE `customer_status_id` IN ($array_customer_dataid)";
			$db->query($sql);
			if($db->affected_rows){
				header("location:".$_SERVER['HTTP_REFERER']);
			}

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