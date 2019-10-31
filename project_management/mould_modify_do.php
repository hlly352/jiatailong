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
	$file_type = $_POST['data_type'];
	$title = $_POST['title'];
	$specification_id = $_POST['specification_id'];
	$t_number = $_POST['t_number'];
	$emailer = $_POST['employeeid'];
	//查找是否有信息
	$is_exists_sql = "SELECT * FROM `db_mould_modify` WHERE `specification_id` = '$specification_id' AND `t_number` = '$t_number'";
	$result_exists = $db->query($is_exists_sql);
	$is_exists = $result_exists->num_rows;
	if($is_exists == 0){
		$sql = "INSERT INTO `db_mould_modify`(`specification_id`,`t_number`) VALUES('$specification_id','$t_number')";
		$db->query($sql);
		if($db->affected_rows){
			$modify_id = $db->insert_id;
		}
	}else{
		$modify_id = $result_exists->fetch_assoc()['modify_id'];
	}
	//限制上传文件的大小
	if($_FILES['file']['size'] > 106132773 || $_FILES['file']['size'] == 0){
		echo '文件超大，请重新上传<a href="mould_modify_edit.php?action=add&from=technology&specification_id='.$specification_id.'">返回</a>';
		return false;
	}
	//上传文件
	if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/mould_modify/".$filedir."/";
			$upload = new upload();
		    $upload->upload_file($upload_path);
		    $array_upload_file = $upload ->array_upload_file;
		    $file_path = $upload_path.$array_upload_file['upload_final_name'];
		    $file_name = $array_upload_file['upload_name'];
		}
		$date = date('Y-m-d');
		$data_info = $title.'#'.$file_name.'#'.$date;
	
	//更改当前模具的改模信息
		$sql = "UPDATE `db_mould_modify` SET `{$file_type}` = CONCAT_WS('&',`{$file_type}`,'".$data_info."'),`{$file_type}_path` = CONCAT_WS('&',`{$file_type}_path`,'".$file_path."') WHERE `modify_id` = '$modify_id'";
	$db->query($sql);
	if($db->affected_rows){
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
				$str_show .= '(\''.$file_type.'\',\''.$employeeid.'\',\''.$modify_id.'\'),';
			}
		}
		$str_show = rtrim($str_show,',');
		$sql_show = "INSERT INTO `db_mould_data_show`(`data_name`,`employeeid`,`modifyid`) VALUES $str_show";

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
		$subject = $mould_number.'的'.$array_mould_modify[$file_type].'发生更新';
		$body = $mould_number.'的'.$array_mould_modify[$file_type].'发生更新,请查看';
		send($send,$address,$subject,$body,$file_path);
		header('location:mould_modify.php');
	}
}elseif($action == 'del'){
	$data = $_GET['data'];
	$modify_id = $_GET['modify_id'];
	$key = $_GET['key'];
	$sql = "SELECT `{$data}`,`{$data}_path` FROM `db_mould_modify` WHERE `modify_id` = '$modify_id'";
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
	//更新删除后的值
	$del_sql = "UPDATE `db_mould_modify` SET {$sql_str} WHERE  FIND_IN_SET('$modify_id',`modify_id`)";

	$db->query($del_sql);
	header('location:'.$_SERVER['HTTP_REFERER']);
}
?>