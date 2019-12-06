<meta http-equiv="content-Type" content="text/html;charset=utf-8">
<?php
	require_once '../global_mysql_connect.php';
	require_once '../function/function.php';
	require_once '../config/config.php';
	require_once '../class/upload.php';
	require_once 'shell.php';
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$informationid = trim($_POST['informationid']);
	$specification_id = trim($_POST['specification_id']);
	$data_name = trim($_POST['data_name']);
	$title = trim($_POST['title']);
	$do_employeeid = trim($_POST['do_employeeid']);
	$checker = trim($_POST['checker']);
	$manager = trim($_POST['manager']);
	$document_no = trim($_POST['document_no']);
	$ueditor = trim(htmlspecialchars($_POST['editorValue']));
	$information_listid = trim($_POST['information_listid']);
	$approvaler = trim($_POST['approvaler']);
	$array_employeeid = $_POST['employeeid'];
	$geter = $array_employeeid?fun_convert_checkbox($_POST['employeeid']):'';
	$date = date('Y-m-d');
	//判断是否项目资料汇总表中是否有信息（db_technical_information）
	$sql_exists = "SELECT `information_id` FROM `db_technical_information` WHERE `specification_id` = '$specification_id'";
	$result_exists = $db->query($sql_exists);
	if($result_exists->num_rows){
		$informationid = $result_exists->fetch_assoc()['information_id'];
	}else{
		$sql_technical = "INSERT INTO `db_technical_information`(`specification_id`) VALUES('$specification_id')";
		$db->query($sql_technical);
		$informationid = $db->insert_id;
	}
	//判断项目是否有除文件以外信息
	// $sql_list_exists = "SELECT * FROM `db_technical_information_list` WHERE `informationid` = '$informationid'";
	// $result_list_exists = $db->query($sql_list_exists);
	// if($result_list_exists->num_rows){
	if(empty($informationid)){
		//更新除文件的信息
		$sql_list_update = "UPDATE `db_technical_information_list` SET `do_employeeid` = '$do_employeeid',`checker` = '$checker',`manager` = '$manager',`approvaler` = '$approvaler',`geter` = '$geter',`employeeid` = '$employeeid',`dodate` = '$date',`ueditor` = '$ueditor',`document_no` = '$document_no' WHERE `information_listid` = '$information_listid'";
		$db->query($sql_list_update);
	}else{
		//插入除文件的信息
		$sql_list_add = "INSERT INTO `db_technical_information_list`(`informationid`,`ueditor`,`data_name`,`do_employeeid`,`checker`,`manager`,`approvaler`,`geter`,`employeeid`,`dodate`,`document_no`) VALUES('$informationid','$ueditor','$data_name','$do_employeeid','$checker','$manager','$approvaler','$geter','$employeeid','$date','$document_no')";
		$db->query($sql_list_add);
		$information_listid  = $db->insert_id;
	}
	//判断资料类型
	$specification_id_str = $specification_id;
	if($data_name == 'project_data' || $data_name == 'project_sum'){
		//搜索已有资料的id
		$sql_info = "SELECT `specification_id` FROM `db_technical_information`";
		$result_info = $db->query($sql_info);
		$array_info = array();
		if($result_info->num_rows){
			while($row_info = $result_info->fetch_assoc()){
				$array_info[] = $row_info['specification_id'];
			}
		}
		//查询当前模具的项目名
		$sql_project_name = "SELECT `project_name` FROM `db_mould_specification` WHERE `mould_specification_id` = '$specification_id'";
		$result_project_name = $db->query($sql_project_name);
		if($result_project_name->num_rows){
			$project_name = $result_project_name->fetch_assoc()['project_name'];
		}
		//查询项目的所有id
		if($project_name){
			$sql_project_id = "SELECT `mould_specification_id` FROM `db_mould_specification` WHERE `project_name` LIKE '%$project_name%' AND `is_approval` = '1'";
			$result_project_id = $db->query($sql_project_id);
			if($result_project_id->num_rows){
				$array_project_id = array();
				while($row_project_id = $result_project_id->fetch_assoc()){
					$specification_ids = $row_project_id['mould_specification_id'];
					$array_project_id[] = $specification_ids;
					//添加不存在的项目汇总信息
					if(!in_array($specification_ids,$array_info)){
					$information_sql = "INSERT INTO `db_technical_information`(`specification_id`) VALUES('$specification_ids')";
					$db->query($information_sql);
					}
				}
			}
		$specification_id_str = fun_convert_checkbox($array_project_id);
		}
	}
		$sql_remark = "UPDATE `db_technical_information` SET `{$data_name}` = '$information_listid'  WHERE  FIND_IN_SET(specification_id,'$specification_id_str')";
	$db->query($sql_remark);
	//限制上传文件的大小
	if($_FILES['file']['size'] > 106132773){
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
		//插入文件信息到表db_technical_informcation_file
		$sql_file = "INSERT INTO `db_technical_information_file`(`informationid`,`data_name`,`title`,`file_name`,`file_path`,`employeeid`,`dodate`) VALUES('$informationid','$data_name','$title','$file_name','$file_path','$employeeid','$date')";
		$db->query($sql_file);
		if($db->affected_rows){
			$information_fileid = $db->insert_id;
			//文件信息插入到总表中 db_technical_information
			$sql_file_add = "UPDATE `db_technical_information` SET `{$data_name}_path` = CONCAT_WS(',',`{$data_name}_path`,'$information_fileid') WHERE FIND_IN_SET(specification_id,'$specification_id_str')";
			$db->query($sql_file_add);
			}
		}
	header('location:project_start.php');

exit;

// 		$data_info = $title.'#'.$file_name.'#'.$date;
// 	var_dump($_POST);exit;
// 	$action = $_REQUEST['action'];
// 	$referer = $_POST['from'];
// 	//删除原来的文件
// 	function del($db,$from,$informationid){
// 		$sql_url = "SELECT {$from} FROM `db_technical_information` WHERE `information_id` = '$informationid'"; 
// 		$result_url = $db->query($sql_url);
// 		if($result_url->num_rows){
// 			$url = $result_url->fetch_row()[0];
// 			if(file_exists($url)){
// 				@unlink($url);
// 			}
// 		}
// 	}
// 	if($action == 'add'){
// 	//获取上传的文件类型
// 	$file_type = $_POST['data_type'];
// 	$doc_type = $_POST['doc_type'];
// 	$title = $_POST['title'];
// 	$specification_id = $_POST['specification_id'];
// 	$emailer = $_POST['employeeid'];
	
// 		//查询当前项目的负责人
// 		$sql_manager = "SELECT `mould_no`,`saler`,`manager` FROM `db_mould_specification` WHERE `mould_specification_id` = '$specification_id'";
// 		$result_manager  = $db->query($sql_manager);
// 		if($result_manager ->num_rows){
// 			$manager = $result_manager->fetch_assoc();
// 			$saler = $manager['saler'];
// 			$mould_number = $manager['mould_no'];
// 			$str_managers = $manager['manager'];
// 			if(stristr($manager['manager'],'$$')){
// 				$managers = explode('$$',$manager['manager']);
// 			}else{
// 				$managers = array();
// 			}
// 			$managers[] = $saler;
// 		}
// 		if(!empty($managers) && !empty($emailer)){
// 			$geter = array_merge($emailer,$managers);
// 		}else{
// 			$geter = array();
// 		}
// 		//拼接邮件信息
// 		$geter = array_unique($geter);
// 		$email_employeeid = array();
// 		foreach($geter as $v){
// 			if($v){
// 				$email_employeeid[] = $v;
// 			}
// 		}
// 		//加入待查看表中
// 		$str_show = '';
// 		foreach($email_employeeid as $employeeid){
// 			if(!empty($employeeid)){
// 				$str_show .= '(\''.$doc_type.'\',\''.$employeeid.'\',\''.$informationid.'\',\''.$file_type.'\'),';
// 			}
// 		}
// 		$str_show = rtrim($str_show,',');
// 		$sql_show = "INSERT INTO `db_mould_data_show`(`data_name`,`employeeid`,`informationid`,`file_type`) VALUES $str_show";
// 		$db->query($sql_show);
// 		//获取邮件发送地址
// 		$address = array();
// 		foreach($email_employeeid as $id){
// 			if($id != ''){
// 				$sql_mail = "SELECT `employeeid`,`email` FROM `db_employee` WHERE `employeeid` = '$id'";
// 				$result_mail = $db->query($sql_mail);
// 				if($result_mail->num_rows){
// 					while($row_mail = $result_mail->fetch_assoc()){
// 						$address[$row_mail['employeeid']] = $row_mail['email'];
// 					}
// 				}
// 			}
// 		}
// 		//发送者的email
// 		$sql_send = "SELECT `email` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
// 		$result_send = $db->query($sql_send);
// 		if($result_send->num_rows){
// 			$send = $result_send->fetch_assoc()['email'];
// 		}else{
// 			$send = 'hr.04@hl.com';
// 		}
// 		$subject = $mould_number.'的'.$array_project_data_type[$file_type][1][$doc_type].'发生更新';
// 		$body = $mould_number.'的'.$array_project_data_type[$file_type][1][$doc_type].'发生更新,请查看';
// 		send($send,$address,$subject,$body,$file_path);
// 	$db->query($sql);
// 	if($db->affected_rows){
// 		switch($referer)
// 			{
// 				case 'technical_information':
// 					$url = 'technical_information.php';
// 				break;
// 				case 'technology':
// 					$url = 'technical_info.php';
// 				break;
// 				case 'project_start':
// 					$url = 'project_start.php';
// 				break;
// 				case 'delivery_service':
// 					$url = 'delivery_service.php';
// 				break;
// 				default:
// 					$url = $_SERVER['HTTP_REFERER'];
// 			}
// 		header('location:'.$url);
// 	}
// }elseif($action == 'del'){
// 	$data = $_GET['data'];
// 	$informationid = $_GET['informationid'];
// 	$key = $_GET['key'];
	
// 	$sql = "SELECT `{$data}`,`{$data}_path` FROM `db_technical_information` WHERE `information_id` = '$informationid'";
// 	$result = $db->query($sql);

// 	function del_str($key,$str){

// 		$start = $end = 0;
// 		for($i = 0;$i<$key;$i++){
// 				$first = strpos($str,'&');
// 				$str = substr($str,$first+1);
// 				echo $str.'<br>';
// 				$start += $first;
// 			}
// 		for($i = 0;$i<$key+1;$i++){
// 				$first = strpos($str,'&');
// 				$str = substr($str,$first+1);
// 				echo $str.'<br>';
// 				$end += $first;
// 			}
// 		$header = substr($str,0,$start);
// 	}
// 	if($result->num_rows){
// 		$new_info = '';	
// 		while($row = $result->fetch_row()){
// 			$paths = explode('&',$row[1]);
// 			$names = explode('&',$row[0]);
// 			foreach($paths as $keys=>$values){
// 				echo $values.'<br>';
// 				//
// 				if($keys == $key){

// 					if(file_exists($values)){
// 						@unlink($values);
// 					}
// 				}else{
// 					$new_path .= $values.'&';
// 					$new_name .= $names[$keys].'&';
// 				}
				
// 			}
			
// 		}
// 	}
// 	$new_path = rtrim($new_path,'&');
// 	$new_name = rtrim($new_name,'&');
// 	$sql_str = "`{$data}` = '$new_name',`{$data}_path` = '$new_path'";
// 	if($data == 'project_data' || $data == 'project_sum'){
// 		//查找当前项目的所有模具
// 				$keyword_sql = "SELECT `db_mould_specification`.`project_name` FROM `db_mould_specification` INNER JOIN `db_technical_information` ON `db_technical_information`.`specification_id` = `db_mould_specification`.`mould_specification_id` WHERE `db_technical_information`.`information_id` = '$informationid'";
// 				$result_keyword = $db->query($keyword_sql);
// 				if($result_keyword->num_rows){
// 					$keyword = $result_keyword->fetch_row()[0];
// 				}
// 				$project_sql = "SELECT `db_technical_information`.`information_id` FROM `db_mould_specification` INNER JOIN `db_technical_information` ON `db_technical_information`.`specification_id` = `db_mould_specification`.`mould_specification_id` WHERE `db_mould_specification`.`project_name` LIKE '%$keyword%'";
// 				$result_project = $db->query($project_sql);
// 				if($result_project->num_rows){
// 					while($row = $result_project->fetch_row()){

// 					$informationids .= $row[0].',';
// 					}
// 				}
// 	$informationid = rtrim($informationids,',');
// 	}
	
// 	//更新删除后的值
// 	$del_sql = "UPDATE `db_technical_information` SET {$sql_str} WHERE  FIND_IN_SET(`information_id`,'$informationid')";

// 	$db->query($del_sql);
// 	header('location:'.$_SERVER['HTTP_REFERER']);
// }
?>