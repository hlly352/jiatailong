<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$work_content = htmlcode($_POST['work_content']);
	$worker = $_POST['employeeid'];
	$issue_date = $_POST['issue_date'];
	$dotime = fun_gettime();
	if($action == "add"){
		$issuer = $_SESSION['employee_info']['employeeid'];
		$sql = "INSERT INTO `db_work` (`workid`,`work_content`,`issue_date`,`issuer`,`worker`,`work_status`,`pdca_status`,`dotime`) VALUES (NULL,'$work_content','$issue_date','$issuer','$worker',1,'P','$dotime')";
		$db->query($sql);
		if($workid = $db->insert_id){
			//员工账号
			$sql_employee = "SELECT `employee_name`,`email` FROM `db_employee` WHERE `employeeid` = '$worker'";
			$result_employee = $db->query($sql_employee);
			if($result_employee->num_rows){
				$array_employee = $result_employee->fetch_assoc();
				$employee_name = $array_employee['employee_name'];
				$email_name = $array_employee['email'];
				$email_subject = "内网PDCA工作任务到达";
				$email_content = "您好，".$employee_name."，你有新的工作任务到达，请登录系统处理。";
				$sql_email = "INSERT INTO `db_email` (`emailid`,`email_name`,`email_subject`,`email_content`,`dotime`) VALUES (NULL,'$email_name','$email_subject','$email_content','$dotime')";
				$db->query($sql_email);
			}
			header("location:my_issue_work.php");
		}
	}elseif($action == "edit"){
		$workid = $_POST['workid'];
		$deadline_date = $_POST['deadline_date'];
		$finish_date = $_POST['finish_date'];
		$pdca_status = $_POST['pdca_status'];
		$work_status = $_POST['work_status'];
		$sql = "UPDATE `db_work` SET `work_content` = '$work_content',`issue_date` = '$issue_date',`deadline_date` = '$deadline_date',`finish_date` = '$finish_date',`worker` = '$worker',`work_status` = '$work_status',`pdca_status` = '$pdca_status' WHERE `workid` = '$workid'";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}elseif($action == "del"){
		$array_workid = $_POST['id'];
		$workid = fun_convert_checkbox($array_workid);
		//删除回复文件
		$sql_reply_file_list = "SELECT `db_upload_file`.`filedir`,`db_upload_file`.`filename` FROM `db_upload_file` INNER JOIN `db_work_reply` ON `db_work_reply`.`replyid` = `db_upload_file`.`linkid` INNER JOIN `db_work_update` ON `db_work_update`.`updateid` = `db_work_reply`.`updateid` WHERE `db_work_update`.`workid` IN ($workid) AND `db_upload_file`.`linkcode` = 'WKRP'";
		$result_reply_file_list = $db->query($sql_reply_file_list);
		if($result_reply_file_list->num_rows){
			while($row_reply_file_list = $result_reply_file_list->fetch_assoc()){
				$reply_filepath = "../upload/file/".$row_reply_file_list['filedir'].'/'.$row_reply_file_list['filename'];
				fun_delfile($reply_filepath);
			}
		}
		//删除更新文件
		$sql_update_file_list = "SELECT `db_upload_file`.`filedir`,`db_upload_file`.`filename` FROM `db_upload_file` INNER JOIN `db_work_update` ON `db_work_update`.`updateid` = `db_upload_file`.`linkid` WHERE `db_work_update`.`workid` IN ($workid) AND `db_upload_file`.`linkcode` = 'WKUP'";
		$result_update_file_list = $db->query($sql_update_file_list);
		if($result_update_file_list->num_rows){
			while($row_update_file_list = $result_update_file_list->fetch_assoc()){
				$update_filepath = "../upload/file/".$row_update_file_list['filedir'].'/'.$row_update_file_list['filename'];
				fun_delfile($update_filepath);
			}
		}
		//删除工作文件
		$sql_work_file_list = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkid` IN ($workid) AND `linkcode` = 'WK'";
		$result_work_file_list = $db->query($sql_work_file_list);
		if($result_work_file_list->num_rows){
			while($row_work_file_list = $result_work_file_list->fetch_assoc()){
				$work_filepath = "../upload/file/".$row_work_file_list['filedir'].'/'.$row_work_file_list['filename'];
				fun_delfile($work_filepath);
			}
		}
		//删除回复文件
		$sql_reply_file = "DELETE `db_upload_file` FROM `db_upload_file` INNER JOIN `db_work_reply` ON `db_work_reply`.`replyid` = `db_upload_file`.`linkid` INNER JOIN `db_work_update` ON `db_work_update`.`updateid` = `db_work_reply`.`updateid` WHERE `db_work_update`.`workid` IN ($workid) AND `db_upload_file`.`linkcode` = 'WKRP'";
		$db->query($sql_reply_file);
		//删除回复
		$sql_reply = "DELETE `db_work_reply` FROM `db_work_reply` INNER JOIN `db_work_update` ON `db_work_update`.`updateid` = `db_work_reply`.`updateid` WHERE `db_work_update`.`workid` IN ($workid)";
		$db->query($sql_reply);
		//删除更新文件
		$sql_update_file = "DELETE `db_upload_file` FROM `db_upload_file` INNER JOIN `db_work_update` ON `db_work_update`.`updateid` = `db_upload_file`.`linkid` WHERE `db_work_update`.`workid` IN ($workid) AND `db_upload_file`.`linkcode` = 'WKUP'";
		$db->query($sql_update_file);
		//删除更新
		$sql_update = "DELETE FROM `db_work_update` WHERE `workid` IN ($workid)";
		$db->query($sql_update);
		//删除工作文件
		$sql_work_file = "DELETE FROM `db_upload_file` WHERE `linkid` IN ($workid) AND `linkcode` = 'WK'";
		$db->query($sql_work_file);
		//删除计划
		$sql_work_plan = "DELETE FROM `db_work_plan` WHERE `workid` IN ($workid)";
		$db->query($sql_work_plan);
		//删除工作
		$sql_work = "DELETE FROM `db_work` WHERE `workid` IN ($workid)";
		$db->query($sql_work);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
	if($workid){
		if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/file/" . $filedir . "/";
			$upload = new upload();
			$upload->upload_files($upload_path);
			if(is_array($array_upload_files = $upload->array_upload_files)){
				for($i=0; $i<count($array_upload_files); $i++){
					$filename = $array_upload_files[$i]['upload_final_name'];
					$upfilename = $array_upload_files[$i]['upload_name'];
					$sql_file = "INSERT INTO `db_upload_file` (`fileid`,`filedir`,`filename`,`upfilename`,`employeeid`,`dotime`,`linkcode`,`linkid`) VALUES (NULL,'$filedir','$filename','$upfilename','$issuer','$dotime','WK','$workid')";
					$db->query($sql_file);
				}
			}
		}
	}
}
?>