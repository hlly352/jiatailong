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
		//接受数据		
		$data = $_POST;
		$new_data = array();
		foreach($data as $key=>$value){
			//把值是数组的转换为字符串
			if(is_array($value)){
				$value = implode("$$",$value);
			}
			$new_data[$key] = $value; 
		}
		unset($new_data['action']);
		unset($new_data['submit']);
		//遍历数组,获取字符串
		$key_word = '';
		$value_word = '';
		 foreach($new_data as $k=>$v){
		 	$key_word .= $k.'`,`';
		 	$value_word .= $v.',';
		 }
		 //拼接数据库字段
		 $key_word = '`'.$key_word;
		 $key_word =  substr($key_word,0,strlen($key_word)-2);
		//拼接要插入到数据库中的值
		 $value_word = str_replace(',','","',$value_word);
		 $value_word = '"'.$value_word;
		 $value_word = substr($value_word,0,strlen($value_word)-2);

		 //图片的存储路径
	           $filedir = date("Ymd");
		$upfiledir = "../upload/mould_image/".$filedir."/";
		 //得到传输的数据
		 if(empty($_FILES['file']['tmp_name'])){
		if($_FILES['file']['name']){
			 //图片上传
			$upload = new upload();
			$upload->upload_files($upfiledir);
			$target_path =  '';
			$target_name = '';
			$final_path = '';
			$upload_final_path = '';
			//图片上传后得到图片的信息
			$upload_info = $upload->array_upload_files;
			//从图片信息中提取图片的存储路径
			foreach($upload_info as $key=>$value){
				foreach($value as $ks=>$vs){
		
				if($ks == 'upload_target_path'){
					$target_path = $vs;
				} elseif($ks == 'upload_final_name'){
					$target_name = $vs;
				}
				$final_path = $target_path.$target_name;
			}
			$upload_final_path .= $final_path.'$';
			}
		}
		}
		}
		//拼接数据库字段
		$key_word .= ',`upload_final_path`';
		//拼接上传数据
		$upload_final_path = substr($upload_final_path,0,strlen($upload_final_path) - 1);
		$value_word .= ',"'.$upload_final_path.'"';

	if($action == 'add'){

		// $sql = "INSERT INTO `db_mould_data` (`mould_dataid`,`mould_name`,`cavity_type`,`part_number`,`t_time`,`p_length`,`p_width`,`p_height`,`p_weight`,`drawing_file`,`lead_time`,`m_length`,`m_width`,`m_height`,`m_weight`,`lift_time`,`tonnage`,`client_name`,`project_name`,`contacts`,`tel`,`email`,`) VALUES (NULL,'$mould_name','$cavity_type','$part_number','$t_time','$p_length','$p_width','$p_height','$p_weight','$drawing_file','$lead_time','$m_length','$m_width','$m_height','$m_weight','$lift_time','$tonnage','$client_name','$project_name','$contacts','$tel','$email')";
		$sql = "INSERT INTO `db_mould_data`($key_word) VALUES($value_word)";
		
		//执行sql语句
		$res = $db->query($sql);
		if($db->insert_id){
			
			header("location:mould_data.php");
		}
	}elseif($action == 'edit'){
		$mould_dataid = $_POST['mould_dataid'];
		$sql = "UPDATE `db_mould_data` SET `mould_name` = '$mould_name',`cavity_type` = '$cavity_type',`part_number` = '$part_number',`t_time` = '$t_time',`p_length` = '$p_length',`p_width` = '$p_width',`p_height` = '$p_height',`p_weight` = '$p_weight',`drawing_file` = '$drawing_file',`lead_time` = '$lead_time',`m_length` = '$m_length',`m_width` = '$m_width',`m_height` = '$m_height',`m_weight` = '$m_weight',`lift_time` = '$lift_time',`tonnage` = '$tonnage',`client_name` = '$client_name',`project_name` = '$project_name',`contacts` = '$contacts',`tel` = '$tel',`email` = '$email' WHERE `mould_dataid` = '$mould_dataid'";
		$db->query($sql);
		if($db->affected_rows){
			header("location:".$_POST['pre_url']);
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