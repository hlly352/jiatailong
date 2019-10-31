<meta http-equiv="content-Type" content="text/html;charset=utf-8">
<?php
	require_once '../global_mysql_connect.php';
	require_once '../function/function.php';
	require_once '../config/config.php';
	require_once '../class/upload.php';
	require_once 'shell.php';
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$action = $_REQUEST['action'];
	$referer = $_POST['from'];
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
	$file_type = $_POST['data_type'];
	$doc_type = $_POST['doc_type'];
	$title = $_POST['title'];
	$specification_id = $_POST['specification_id'];
	$emailer = $_POST['employeeid'];
	//查找是否有信息
	$is_exists_sql = "SELECT * FROM `db_technical_information` WHERE `specification_id` = '$specification_id'";
	$result_exists = $db->query($is_exists_sql);
	$is_exists = $result_exists->num_rows;
	if($is_exists == 0){
		$sql = "INSERT INTO `db_technical_information`(`specification_id`) VALUES('$specification_id')";
		$db->query($sql);
		if($db->affected_rows){
			$informationid = $db->insert_id;
		}
	}else{
		$informationid = $result_exists->fetch_assoc()['information_id'];
	}
	//限制上传文件的大小
	if($_FILES['file']['size'] > 106132773 || $_FILES['file']['size'] == 0){
		echo '文件超大，请重新上传<a href="technical_information_edit.php?action=add&from=technology&specification_id='.$specification_id.'">返回</a>';
		return false;
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
		$data_info = $title.'#'.$file_name.'#'.$date.'#'.$file_type;
	//把地址填入到对应的表中
	if($doc_type == 'project_data' || $doc_type == 'project_sum'){
		//搜索已有的资料id
		$info_sql = "SELECT `specification_id` FROM `db_technical_information`";
		$result_info = $db->query($info_sql);
		if($result_info->num_rows){
			$arr_info = array();
			while($row_info = $result_info ->fetch_assoc()){
				$arr_info[] = $row_info['specification_id'];
			}
		}
	//查找当前项目的项目名称
		$keyword_sql = "SELECT `db_mould_specification`.`project_name` FROM `db_mould_specification` INNER JOIN `db_technical_information` ON `db_technical_information`.`specification_id` = `db_mould_specification`.`mould_specification_id` WHERE `db_technical_information`.`information_id` = '$informationid'";
		$result_keyword = $db->query($keyword_sql);
		if($result_keyword->num_rows){
			$keyword = $result_keyword->fetch_row()[0];
			}
	//当前项目的所有项目
		$project_sql = "SELECT `db_mould_specification`.`mould_specification_id` FROM `db_mould_specification` WHERE `project_name` LIKE '%$keyword%' AND `is_approval` = '1'";
		$result_project = $db->query($project_sql);
	//新建当前项目中没有的模具信息
		if($result_project->num_rows){
			$array_specification_id = array();
			while($row = $result_project->fetch_assoc()){
				$array_specification_id[] = $row['mould_specification_id'];
				$specification_ids = $row['mould_specification_id'];
				//新建技术资料
				if(!in_array($specification_ids,$arr_info)){
					$information_sql = "INSERT INTO `db_technical_information`(`specification_id`) VALUES('".$row['mould_specification_id']."')";
					$db->query($information_sql);
					}
				}
			}
		$specification_id_str = fun_convert_checkbox($array_specification_id);
		$sql = "UPDATE `db_technical_information` SET `{$doc_type}` = CONCAT_WS('&',`{$doc_type}`,'".$data_info."'),`{$doc_type}_path` = CONCAT_WS('&',`{$doc_type}_path`,'".$file_path."')  WHERE  FIND_IN_SET(specification_id,'$specification_id_str')";
	
	}else{
		$sql = "UPDATE `db_technical_information` SET `{$doc_type}` = CONCAT_WS('&',`{$doc_type}`,'".$data_info."'),`{$doc_type}_path` = CONCAT_WS('&',`{$doc_type}_path`,'".$file_path."') WHERE `information_id` = '$informationid'";
	}

		//查询当前项目的负责人
		$sql_manager = "SELECT `mould_no`,`saler`,`manager` FROM `db_mould_specification` WHERE `mould_specification_id` = '$specification_id'";
		$result_manager  = $db->query($sql_manager);
		if($result_manager ->num_rows){
			$manager = $result_manager->fetch_assoc();
			$saler = $manager['saler'];
			$mould_number = $manager['mould_no'];
			$str_managers = $manager['manager'];
			if(stristr($manager['manager'],'$$')){
				$managers = explode('$$',$manager['manager']);
			}else{
				$managers = array();
			}
			$managers[] = $saler;
		}
		if(!empty($managers) && !empty($emailer)){
			$geter = array_merge($emailer,$managers);
		}else{
			$geter = array();
		}
		//拼接邮件信息
		$geter = array_unique($geter);
		$email_employeeid = array();
		foreach($geter as $v){
			if($v){
				$email_employeeid[] = $v;
			}
		}
		//加入待查看表中
		$str_show = '';
		foreach($email_employeeid as $employeeid){
			if(!empty($employeeid)){
				$str_show .= '(\''.$doc_type.'\',\''.$employeeid.'\',\''.$informationid.'\',\''.$file_type.'\'),';
			}
		}
		$str_show = rtrim($str_show,',');
		$sql_show = "INSERT INTO `db_mould_data_show`(`data_name`,`employeeid`,`informationid`,`file_type`) VALUES $str_show";
		$db->query($sql_show);
		//获取邮件发送地址
		$address = array();
		foreach($email_employeeid as $id){
			if($id != ''){
				$sql_mail = "SELECT `employeeid`,`email` FROM `db_employee` WHERE `employeeid` = '$id'";
				$result_mail = $db->query($sql_mail);
				if($result_mail->num_rows){
					while($row_mail = $result_mail->fetch_assoc()){
						$address[$row_mail['employeeid']] = $row_mail['email'];
					}
				}
			}
		}
		//发送者的email
		$sql_send = "SELECT `email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
		$result_send = $db->query($sql_send);
		if($result_send->num_rows){
			$send = $result_send->fetch_assoc()['email'];
		}else{
			$send = 'hr.04@hl.com';
		}
		$subject = $mould_number.'的'.$array_project_data_type[$file_type][1][$doc_type].'发生更新';
		$body = $mould_number.'的'.$array_project_data_type[$file_type][1][$doc_type].'发生更新,请查看';
		send($send,$address,$subject,$body,$file_path);
	$db->query($sql);
	if($db->affected_rows){
		switch($referer)
			{
				case 'technical_information':
					$url = 'technical_information.php';
				break;
				case 'technology':
					$url = 'technical_info.php';
				break;
				case 'project_start':
					$url = 'project_start.php';
				break;
				case 'delivery_service':
					$url = 'delivery_service.php';
				break;
				default:
					$url = $_SERVER['HTTP_REFERER'];
			}
		header('location:'.$url);
	}
}elseif($action == 'del'){
	$data = $_GET['data'];
	$informationid = $_GET['informationid'];
	$key = $_GET['key'];
	
	$sql = "SELECT `{$data}`,`{$data}_path` FROM `db_technical_information` WHERE `information_id` = '$informationid'";
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
		while($row = $result->fetch_row()){
			$paths = explode('&',$row[1]);
			$names = explode('&',$row[0]);
			foreach($paths as $keys=>$values){
				echo $values.'<br>';
				//
				if($keys == $key){

					if(file_exists($values)){
						@unlink($values);
					}
				}else{
					$new_path .= $values.'&';
					$new_name .= $names[$keys].'&';
				}
				
			}
			
		}
	}
	$new_path = rtrim($new_path,'&');
	$new_name = rtrim($new_name,'&');
	$sql_str = "`{$data}` = '$new_name',`{$data}_path` = '$new_path'";
	if($data == 'project_data' || $data == 'project_sum'){
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
	$del_sql = "UPDATE `db_technical_information` SET {$sql_str} WHERE  FIND_IN_SET(`information_id`,'$informationid')";

	$db->query($del_sql);
	header('location:'.$_SERVER['HTTP_REFERER']);
}
?>