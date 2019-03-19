<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$notice_typeid = $_POST['notice_typeid'];
	$notice_title = $_POST['notice_title'];
	$notice_content = $_POST['notice_content'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$sql = "INSERT INTO `db_notice` (`noticeid`,`notice_title`,`notice_content`,`notice_status`,`notice_typeid`,`employeeid`,`dotime`) VALUES (NULL,'$notice_title','$notice_content',1,'$notice_typeid','$employeeid','$dotime')";
		$db->query($sql);
		if($noticeid = $db->insert_id){
			header("location:notice.php");
		}
	}elseif($action == "edit"){
		$noticeid = $_POST['noticeid'];
		$notice_status = $_POST['notice_status'];
		$sql = "UPDATE `db_notice` SET `notice_title` = '$notice_title',`notice_content` = '$notice_content',`notice_status` = '$notice_status',`notice_typeid` = '$notice_typeid' WHERE `noticeid` = '$noticeid'";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}elseif($action == "del"){
		$array_noticeid = $_POST['id'];
		$noticeid = fun_convert_checkbox($array_noticeid);
		$sql_file = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkcode` = 'NT' AND `linkid` IN ($noticeid)";
		$result_file = $db->query($sql_file);
		if($result_file->num_rows){
			while($row_file = $result_file->fetch_assoc()){
				$filedir = $row_file['filedir'];
				$filename = $row_file['filename'];
				$filepath = "../upload/file/".$filedir.'/'.$filename;
				fun_delfile($filepath);
			}
		}
		$sql_file_list = "DELETE FROM `db_upload_file` WHERE `linkcode` = 'NT' AND `linkid` IN ($noticeid)";
		$db->query($sql_file_list);
		$sql = "DELETE FROM `db_notice` WHERE `noticeid` IN ($noticeid)";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}
	if($noticeid){
		if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/file/".$filedir."/";
			$linkcode = 'NT';
			$upload = new upload();
			$upload->upload_files($upload_path);
			if(is_array($array_upload_files = $upload->array_upload_files)){
				for($i=0; $i<count($array_upload_files); $i++){
					$filename = $array_upload_files[$i]['upload_final_name'];
					$upfilename = $array_upload_files[$i]['upload_name'];
					$sql_file = "INSERT INTO `db_upload_file` (`fileid`,`filedir`,`filename`,`upfilename`,`employeeid`,`dotime`,`linkcode`,`linkid`) VALUES (NULL,'$filedir','$filename','$upfilename','$employeeid','$dotime','$linkcode','$noticeid')";
					$db->query($sql_file);
				}
			}
		}
	}
}
?>