<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$reply_content = htmlcode($_POST['reply_content']);
	if($action == "add"){
		$updateid = $_POST['updateid'];
		$employeeid = $_SESSION['employee_info']['employeeid'];
		$dotime = fun_gettime();
		$sql = "INSERT INTO `db_work_reply` (`replyid`,`reply_content`,`updateid`,`employeeid`,`dotime`) VALUES (NULL,'$reply_content','$updateid','$employeeid','$dotime')";
		$db->query($sql);
		if($replyid = $db->insert_id){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
	}elseif($action == "edit"){
		$replyid = $_POST['replyid'];
		$sql = "UPDATE `db_work_reply` SET `reply_content` = '$reply_content' WHERE `replyid` = '$replyid'";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}elseif($action == "del"){
		$updateid = $_POST['updateid'];
		$array_replyid = $_POST['replyid'];
		$replyid = fun_convert_checkbox($array_replyid);
		//删除附件文件
		$sql_file_list = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkid` IN ($replyid) AND `linkcode` = 'WKRP'";
		$result_file_list = $db->query($sql_file_list);
		if($result_file_list->num_rows){
			while($row_file_list = $result_file_list->fetch_assoc()){
				$filepath = "../upload/file/".$row_file_list['filedir'].'/'.$row_file_list['filename'];
				fun_delfile($filepath);
			}
		}
		$sql_file = "DELETE FROM `db_upload_file` WHERE `linkid` IN ($replyid) AND `linkcode` = 'WKRP'";
		$db->query($sql_file);
		$sql = "DELETE FROM `db_work_reply` WHERE `replyid` IN ($replyid)";
		$db->query($sql);
		if($db->affected_rows){
			header("location:work_replyae.php?id=".$updateid."&action=add");
		}
	}
	if($replyid){
		if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/file/" . $filedir . "/";
			$upload = new upload();
			$upload->upload_files($upload_path);
			if(is_array($array_upload_files = $upload->array_upload_files)){
				for($i=0; $i<count($array_upload_files); $i++){
					$filename = $array_upload_files[$i]['upload_final_name'];
					$upfilename = $array_upload_files[$i]['upload_name'];
					$sql_file = "INSERT INTO `db_upload_file` (`fileid`,`filedir`,`filename`,`upfilename`,`employeeid`,`dotime`,`linkcode`,`linkid`) VALUES (NULL,'$filedir','$filename','$upfilename','$employeeid','$dotime','WKRP','$replyid')";
					$db->query($sql_file);
				}
			}
		}
	}
}
?>