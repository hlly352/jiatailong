<meta http-equiv="content-Type" content="text/html;charset=utf-8">
<?php
	require_once '../global_mysql_connect.php';
	require_once '../function/function.php';
	require_once '../class/upload.php';
	require_once 'shell.php';
	$action = $_REQUEST['action'];
	//删除原来的文件
	function del($db,$from,$informationid){
		$sql_url = "SELECT {$from} FROM `db_technical_information` WHERE `information_id` = '$informationid'"; 
		$result_url = $db->query($sql_url);
		if($result_url->num_rows){
			$url = $result_url->fetch_row()[0];
			if(file_exists($url)){
				@unlink($url);
			}
		}
	}
	if($action == 'add'){
	//获取上传的文件类型
	$file_type = $_POST['file_type'];
	$mouldid = $_POST['mouldid'];
	$title = $_POST['title'];
	$specification_id = $_POST['specification_id'];
	//查找是否有信息
	$is_exists_sql = "SELECT * FROM `db_technical_information` WHERE `specification_id` = '$specification_id'";
	$result_exists = $db->query($is_exists_sql);
	$is_exists = $result_exists->num_rows;
	if($is_exists == 0){
		$sql = "INSERT INTO `db_technical_information`(`mouldid`,`specification_id`) VALUES('$mouldid','$specification_id')";
		$db->query($sql);
		if($db->affected_rows){
			$informationid = $db->insert_id;
		}
	}else{
		$informationid = $result_exists->fetch_assoc()['information_id'];
	}
	//上传文件
	if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/technical_information/".$filedir."/";
			$upload = new upload();
		    $upload->upload_file($upload_path);
		    $array_upload_file = $upload ->array_upload_file;
		    $file_path = $upload_path.$array_upload_file['upload_final_name'];
		    $file_name = $array_upload_file['upload_name'];
		}
		$date = date('Y-m-d');
	//把地址填入到对应的表中
	switch($file_type)
		{
			case 'project_data':
				//搜索已有的资料id
				$info_sql = "SELECT `specification_id` FROM `db_technical_information`";
				$result_info = $db->query($info_sql);
				if($result_info->num_rows){
					$arr_info = array();
					while($row_info = $result_info ->fetch_assoc()){
						$arr_info[] = $row_info['specification_id'];
					}
				}
				//查找当前项目的所有模具
				$keyword_sql = "SELECT `db_mould_specification`.`project_name` FROM `db_mould_specification` INNER JOIN `db_technical_information` ON `db_technical_information`.`specification_id` = `db_mould_specification`.`mould_specification_id` WHERE `db_technical_information`.`information_id` = '$informationid'";
				$result_keyword = $db->query($keyword_sql);
				if($result_keyword->num_rows){
					$keyword = $result_keyword->fetch_row()[0];
				}
				$project_sql = "SELECT `db_mould_specification`.`mould_specification_id`,`mould_id` FROM `db_mould_specification` WHERE `project_name` LIKE '%$keyword%'";
				$result_project = $db->query($project_sql);
				if($result_project->num_rows){
					$array_specification_id = array();
					while($row = $result_project->fetch_assoc()){
						$array_specification_id[] = $row['mould_specification_id'];
						$specification_ids = $row['mould_specification_id'];
						//新建技术资料
						if(!in_array($specification_ids,$arr_info)){
							$information_sql = "INSERT INTO `db_technical_information`(`specification_id`,`mouldid`) VALUES('".$row['mould_specification_id']."','".$row['mould_id']."')";
							$db->query($information_sql);
						}
					}
				}
				$specification_id_str = fun_convert_checkbox($array_specification_id);
				$sql = "UPDATE `db_technical_information` SET `project_data` = CONCAT_WS('&',`project_data`,'$file_path'),`project_data_name` = CONCAT_WS('&',`project_data_name`,'$file_name'),`project_data_date` = CONCAT_WS('&',`project_data_date`,'$date'),`project_data_title` = CONCAT_WS('&',`project_data_title`,'$title') WHERE  FIND_IN_SET(specification_id,'$specification_id_str')";
		
			break;
			case 'mould_data':
				$sql = "UPDATE `db_technical_information` SET `mould_data` = CONCAT_WS('&',`mould_data`,'$file_path'),`mould_data_name` = CONCAT_WS('&',`mould_data_name`,'$file_name'),`mould_data_date` = CONCAT_WS('&',`mould_data_date`,'$date'),`mould_data_title` = CONCAT_WS('&',`mould_data_title`,'$title') WHERE `information_id` = '$informationid'";
			break;
			case 'flow':
				$sql = "UPDATE `db_technical_information` SET `flow` = CONCAT_WS('&',`flow`,'$file_path'),`flow_name` = CONCAT_WS('&',`flow_name`,'$file_name'),`flow_date` = CONCAT_WS('&',`flow_date`,'$date'),`flow_title` = CONCAT_WS('&',`flow_title`,'$title') WHERE `information_id` = '$informationid'";
			break;
			case 'report':
				$sql = "UPDATE `db_technical_information` SET `report` = CONCAT_WS('&',`report`,'$file_path'),`report_name` = CONCAT_WS('&',`report_name`,'$file_name'),`report_date` = CONCAT_WS('&',`report_date`,'$date'),`report_title` = CONCAT_WS('&',`report_title`,'$title') WHERE `information_id` = '$informationid'";
			break;
			case 'standard':
				$sql = "UPDATE `db_technical_information` SET `standard` = CONCAT_WS('&',`standard`,'$file_path'),`standard_name` = CONCAT_WS('&',`standard_name`,'$file_name'),`standard_date` = CONCAT_WS('&',`standard_date`,'$date'),`standard_title` = CONCAT_WS('&',`standard_title`,'$title') WHERE `information_id` = '$informationid'";
			break;
			case 'drawing':
				$sql = "UPDATE `db_technical_information` SET `drawing` = CONCAT_WS('&',`drawing`,'$file_path'),`drawing_name` = CONCAT_WS('&',`drawing_name`,'$file_name'),`drawing_date` = CONCAT_WS('&',`drawing_date`,'$date'),`drawing_title` = CONCAT_WS('&',`drawing_title`,'$title') WHERE `information_id` = '$informationid'";
			break;
		}

	$db->query($sql);
	if($db->affected_rows){
		header('location:technical_information.php');
	}
}elseif($action == 'del'){
	$data = $_GET['data'];
	$informationid = $_GET['informationid'];
	$key = $_GET['key'];

	$sql = "SELECT `{$data}`,`{$data}_name`,`{$data}_title`,`{$data}_date` FROM `db_technical_information` WHERE `information_id` = '$informationid'";
	$result = $db->query($sql);
	function del_str($key,$str){

		$start = $end = 0;
		for($i = 0;$i<$key;$i++){
				$first = strpos($str,'&');
				$str = substr($str,$first+1);
				echo $str.'<br>';
				$start += $first;
			}
		for($i = 0;$i<$key+1;$i++){
				$first = strpos($str,'&');
				$str = substr($str,$first+1);
				echo $str.'<br>';
				$end += $first;
			}
		$header = substr($str,0,$start);
	}
	if($result->num_rows){
		$new_info = '';	
		while($row = $result->fetch_assoc()){
			foreach($row as $keys=>$values){
				//转换为数组
				$array_data = explode('&',$values);
				if(stripos($array_data[$key],'/upload/technical_information/')){
					if(file_exists($array_data[$key])){
						@unlink($array_data[$key]);
					}
				}
				unset($array_data[$key]);
				$new_data = implode('&',$array_data);
				$new_info .= '`'.$keys.'`="'.$new_data.'",';
			}
			
		}
	}
	$new_info = rtrim($new_info,',');
	if($data == 'project_data'){
		//查找当前项目的所有模具
				$keyword_sql = "SELECT `db_mould_specification`.`project_name` FROM `db_mould_specification` INNER JOIN `db_technical_information` ON `db_technical_information`.`specification_id` = `db_mould_specification`.`mould_specification_id` WHERE `db_technical_information`.`information_id` = '$informationid'";
				$result_keyword = $db->query($keyword_sql);
				if($result_keyword->num_rows){
					$keyword = $result_keyword->fetch_row()[0];
				}
				$project_sql = "SELECT `db_technical_information`.`information_id` FROM `db_mould_specification` INNER JOIN `db_technical_information` ON `db_technical_information`.`specification_id` = `db_mould_specification`.`mould_specification_id` WHERE `db_mould_specification`.`project_name` LIKE '%$keyword%'";
				$result_project = $db->query($project_sql);
				if($result_project->num_rows){
					while($row = $result_project->fetch_row()){

					$informationids .= $row[0].',';
					}
				}
	$informationid = rtrim($informationids,',');
	}
	
	//更新删除后的值
	$del_sql = "UPDATE `db_technical_information` SET {$new_info} WHERE  FIND_IN_SET(`information_id`,'$informationid')";

	$db->query($del_sql);
	header('location:'.$_SERVER['HTTP_REFERER']);
}
?>