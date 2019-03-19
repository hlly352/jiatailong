<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$update_date = $_POST['update_date'];
	$update_content = htmlcode($_POST['update_content']);
	$workid = $_POST['workid'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$sql = "INSERT INTO `db_routine_work_update` (`updateid`,`update_date`,`update_content`,`workid`,`employeeid`,`dotime`) VALUES (NULL,'$update_date','$update_content','$workid','$employeeid','$dotime')";
		$db->query($sql);
		if($updateid = $db->insert_id){
			header("location:routine_work_update.php?id=".$workid."&date=".$update_date);
		}
	}elseif($action == "edit"){
		$updateid = $_POST['updateid'];
		$sql = "UPDATE `db_routine_work_update` SET `update_content` = '$update_content' WHERE `updateid` = '$updateid'";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}elseif($action == "del"){
		$array_updateid = fun_convert_checkbox($_POST['id']);
		//删除更新文件
		$sql_update_file_list = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkid` IN ($array_updateid) AND `linkcode` = 'RWUP'";
		$result_update_file_list = $db->query($sql_update_file_list);
		if($result_update_file_list->num_rows){
			while($row_update_file_list = $result_update_file_list->fetch_assoc()){
				$update_filepath = "../upload/file/".$row_update_file_list['filedir'].'/'.$row_update_file_list['filename'];
				fun_delfile($update_filepath);
			}
		}
		//删除更新文件记录
		$sql_update_file = "DELETE FROM `db_upload_file` WHERE `linkid` IN ($array_updateid) AND `linkcode` = 'RWUP'";
		$db->query($sql_update_file);
		//删除更新
		$sql_update = "DELETE FROM `db_routine_work_update` WHERE `updateid` IN ($array_updateid)";
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
					$sql_file = "INSERT INTO `db_upload_file` (`fileid`,`filedir`,`filename`,`upfilename`,`employeeid`,`dotime`,`linkcode`,`linkid`) VALUES (NULL,'$filedir','$filename','$upfilename','$employeeid','$dotime','RWUP','$updateid')";
					$db->query($sql_file);
				}
			}
		}
	}
}
?>