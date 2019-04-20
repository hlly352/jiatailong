<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';

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
		$key_word = ' ';
		$value_word = ' ';
		//拼接sql 语句的字段和值
		foreach($customer_info as $key => $value){
			$key_word .= '`'.$key.'`,';
			$value_word .= '"'.$value.'",';
		}

		$key_word = $key_word.'`add_time`';
		$value_word = $value_word.time();
		$sql = "INSERT INTO `db_customer_info` ($key_word) VALUES($value_word)";
		//执行sql 语句
		$result = $db->query($sql);

		if($db->insert_id){
				
			header("location:customer_index.php");
		}
	}elseif($action == 'edit'){
		//报价单号
		$mold_id = FLOOR(RAND()*9000+1000);
		//拼接数据库字段
		//判断是否修改了图片
		if(($_FILES['file']['tmp_name'][0]) != null){
			$key_word .= ',`upload_final_path`,`time`,`mold_id`';
			//拼接上传数据
			$upload_final_path = substr($upload_final_path,0,strlen($upload_final_path) - 1);
			$value_word .= ',"'.$upload_final_path.'",'.time().','.$mold_id;
		} else {
			$key_word .= ',`time`,`mold_id`';
			//拼接上传数据
			$value_word .= ','.time().','.$mold_id;
		}
		
		$key_arr = explode(',',$key_word);
		$value_arr = explode(',',$value_word);
		$result_arr = array_combine($key_arr,$value_arr);
		$str = '';
		foreach($result_arr as $key=>$val){
			$str .= $key.'='.$val.',';
		}
		//拼接更新数据库的sql语句
		$str = substr($str,0,strlen($str) - 1);
		$sql = "UPDATE `db_mould_data` SET".$str." WHERE `mould_dataid` = ".$mould_dataid;
	
		$res = $db->query($sql);
		if($res){
			header("location:mould_data.php");
		}
		/*if($db->insert_id){
			header("location:mould_data.php");
		}
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}*/
	}elseif($action == 'approval'){
		//报价单号
		$mold_id = FLOOR(RAND()*9000+1000);
		//拼接数据库字段
		if(($_FILES['file']['tmp_name'][0]) != null){
			$key_word .= ',`upload_final_path`,`time`,`mold_id`,`is_approval`';
			//拼接上传数据
			$upload_final_path = substr($upload_final_path,0,strlen($upload_final_path) - 1);
			$value_word .= ',"'.$upload_final_path.'",'.time().','.$mold_id.',"1"';
		} else {
			$key_word .= ',`time`,`mold_id`,`is_approval`';
			//拼接上传数据
			$value_word .= ','.time().','.$mold_id.',"1"';
		}
		
		$key_arr = explode(',',$key_word);
		$value_arr = explode(',',$value_word);
		$result_arr = array_combine($key_arr,$value_arr);
		$str = '';
		foreach($result_arr as $key=>$val){
			$str .= $key.'='.$val.',';
		}
		//拼接更新数据库的sql语句
		$str = substr($str,0,strlen($str) - 1);
		$sql = "UPDATE `db_mould_data` SET".$str." WHERE `mould_dataid` = ".$mould_dataid;
		
		$res = $db->query($sql);
		if($res){
			header("location:mould_data_approval.php");
		}
		/*************************/
			//拼接数据库字段
		//$key_word .= ',`time`';
		//拼接上传数据
		//$value_word .= ','.time();
		//echo substr($value_word;exit;
	
		//echo $sql;//exit;
		//$mould_dataid = $_POST['mould_dataid'];
		//$sql = "UPDATE `db_mould_data` SET `mould_name` = '$mould_name',`cavity_type` = '$cavity_type',`part_number` = '$part_number',`t_time` = '$t_time',`p_length` = '$p_length',`p_width` = '$p_width',`p_height` = '$p_height',`p_weight` = '$p_weight',`drawing_file` = '$drawing_file',`lead_time` = '$lead_time',`m_length` = '$m_length',`m_width` = '$m_width',`m_height` = '$m_height',`m_weight` = '$m_weight',`lift_time` = '$lift_time',`tonnage` = '$tonnage',`client_name` = '$client_name',`project_name` = '$project_name',`contacts` = '$contacts',`tel` = '$tel',`email` = '$email' WHERE `mould_dataid` = '$mould_dataid'";
		//$res = $db->query($sql);
		//if($db->insert_id){
		//	header("location:mould_data.php");
		//}
		/*if($db->affected_rows){
			header("location:".$_POST['pre_url']);
		}*/
	} elseif($action == 'approval_edit'){
		//拼接数据库字段
		if(($_FILES['file']['tmp_name'][0]) != null){
			$key_word .= ',`upload_final_path`,`time`,`is_approval`';
			//拼接上传数据
			$upload_final_path = substr($upload_final_path,0,strlen($upload_final_path) - 1);
			$value_word .= ',"'.$upload_final_path.'",'.time().',"1"';
		} else {
			$key_word .= ',`time`,`is_approval`';
			//拼接上传数据
			$value_word .= ','.time().',"1"';
		}
			$sql = "INSERT INTO `db_mould_data`($key_word) VALUES($value_word)";
			$db->query($sql);
		if($db->insert_id){
			$id = $db->insert_id;		
			header("location:mould_data_approval.php");
		}
	}elseif($action == 'del'){
		$array_mould_dataid = fun_convert_checkbox($_POST['id']);
		$sql_list = "DELETE `db_mould_quote_list` FROM `db_mould_quote_list` INNER JOIN `db_mould_quote` ON `db_mould_quote`.`quoteid` = `db_mould_quote_list`.`quoteid` WHERE `db_mould_quote`.`mould_dataid` IN ($array_mould_dataid)";
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
		$sql = "DELETE FROM `db_mould_data` WHERE `mould_dataid` IN ($array_mould_dataid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	} 
}
?>