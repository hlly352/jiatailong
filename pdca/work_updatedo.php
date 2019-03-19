<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$update_type = $_POST['update_type'];
	$delay_date = $_POST['delay_date'];
	$update_content = htmlcode($_POST['update_content']);
	$employee = trim($_POST['employee']);
	$workid = $_POST['workid'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$sql = "INSERT INTO `db_work_update` (`updateid`,`update_type`,`delay_date`,`update_content`,`employee`,`workid`,`employeeid`,`dotime`) VALUES (NULL,'$update_type','$delay_date','$update_content','$employee','$workid','$employeeid','$dotime')";
		$db->query($sql);
		if($updateid = $db->insert_id){
			header("location:work_update.php?id=".$workid);
		}
	}elseif($action == "edit"){
		$updateid = $_POST['updateid'];
		$sql = "UPDATE `db_work_update` SET `update_content` = '$update_content',`update_type` = '$update_type',`delay_date` = '$delay_date',`employee` = '$employee' WHERE `updateid` = '$updateid'";
		$db->query($sql);
		header("location:work_update.php?id=".$workid);
	}elseif($action == "del"){
		$array_updateid = $_POST['updateid'];
		$updateid = fun_convert_checkbox($array_updateid);
		//批示文件删除
		$sql_reply_file_list = "SELECT `db_upload_file`.`filedir`,`db_upload_file`.`filename` FROM `db_upload_file` INNER JOIN `db_work_reply` ON `db_work_reply`.`replyid` = `db_upload_file`.`linkid` WHERE `db_upload_file`.`linkcode` = 'WKRP' AND `db_work_reply`.`updateid` IN ($updateid)";
		$result_reply_file_list = $db->query($sql_reply_file_list);
		if($result_reply_file_list->num_rows){
			while($row_reply_file_list = $result_reply_file_list->fetch_assoc()){
				$reply_filepath = "../upload/file/".$row_reply_file_list['filedir'].'/'.$row_reply_file_list['filename'];
				fun_delfile($reply_filepath);
			}
		}
		
		//删除更新文件
		$sql_update_file_list = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkid` IN ($updateid) AND `linkcode` = 'WKUP'";
		$result_update_file_list = $db->query($sql_update_file_list);
		if($result_update_file_list->num_rows){
			while($row_update_file_list = $result_update_file_list->fetch_assoc()){
				$update_filepath = "../upload/file/".$row_update_file_list['filedir'].'/'.$row_update_file_list['filename'];
				fun_delfile($update_filepath);
			}
		}
		//删除回复文件记录
		$sql_reply_file = "DELETE `db_upload_file` FROM `db_upload_file` INNER JOIN `db_work_reply` ON `db_work_reply`.`replyid` = `db_upload_file`.`linkid`  WHERE `db_work_reply`.`updateid` IN ($updateid) AND `db_upload_file`.`linkcode` = 'WKRP'";
		$db->query($sql_reply_file);
		//删除回复
		$sql_reply = "DELETE FROM `db_work_reply` WHERE `updateid` IN ($updateid)";
		$db->query($sql_reply);
		//删除更新文件记录
		$sql_update_file = "DELETE FROM `db_upload_file` WHERE `linkid` IN ($updateid) AND `linkcode` = 'WKUP'";
		$db->query($sql_update_file);
		//删除更新
		$sql_update = "DELETE FROM `db_work_update` WHERE `updateid` IN ($updateid)";
		$db->query($sql_update);
		if($db->affected_rows){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}
	if($updateid){
		if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/file/" . $filedir . "/";
			$upload = new upload();
			$upload->upload_files($upload_path);
			if(is_array($array_upload_files = $upload->array_upload_files)){
				for($i=0; $i<count($array_upload_files); $i++){
					$filename = $array_upload_files[$i]['upload_final_name'];
					$upfilename = $array_upload_files[$i]['upload_name'];
					$sql_file = "INSERT INTO `db_upload_file` (`fileid`,`filedir`,`filename`,`upfilename`,`employeeid`,`dotime`,`linkcode`,`linkid`) VALUES (NULL,'$filedir','$filename','$upfilename','$employeeid','$dotime','WKUP','$updateid')";
					$db->query($sql_file);
				}
			}
		}
	}
}
?>