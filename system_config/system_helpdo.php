<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$systemid = $_POST['systemid'];
	$help_title = $_POST['help_title'];
	$help_content = $_POST['help_content'];
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($action == "add"){
		$sql = "INSERT INTO `db_system_help` (`helpid`,`help_title`,`help_content`,`help_status`,`systemid`,`employeeid`,`dotime`) VALUES (NULL,'$help_title','$help_content',1,'$systemid','$employeeid','$dotime')";
		$db->query($sql);
		if($helpid = $db->insert_id){
			header("location:system_help.php");
		}
	}elseif($action == "edit"){
		$helpid = $_POST['helpid'];
		$help_status = $_POST['help_status'];
		$sql = "UPDATE `db_system_help` SET `help_title` = '$help_title',`help_content` = '$help_content',`help_status` = '$help_status',`systemid` = '$systemid' WHERE `helpid` = '$helpid'";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}elseif($action == "del"){
		$array_helpid = $_POST['id'];
		$helpid = fun_convert_checkbox($array_helpid);
		$sql_file = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkcode` = 'SH' AND `linkid` IN ($helpid)";
		$result_file = $db->query($sql_file);
		if($result_file->num_rows){
			while($row_file = $result_file->fetch_assoc()){
				$filedir = $row_file['filedir'];
				$filename = $row_file['filename'];
				$filepath = "../upload/file/".$filedir.'/'.$filename;
				fun_delfile($filepath);
			}
		}
		$sql_file_list = "DELETE FROM `db_upload_file` WHERE `linkcode` = 'SH' AND `linkid` IN ($helpid)";
		$db->query($sql_file_list);
		$sql = "DELETE FROM `db_system_help` WHERE `helpid` IN ($helpid)";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}
	if($helpid){
		if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/file/".$filedir."/";
			$linkcode = 'SH';
			$upload = new upload();
			$upload->upload_files($upload_path);
			if(is_array($array_upload_files = $upload->array_upload_files)){
				for($i=0; $i<count($array_upload_files); $i++){
					$filename = $array_upload_files[$i]['upload_final_name'];
					$upfilename = $array_upload_files[$i]['upload_name'];
					$sql_file = "INSERT INTO `db_upload_file` (`fileid`,`filedir`,`filename`,`upfilename`,`employeeid`,`dotime`,`linkcode`,`linkid`) VALUES (NULL,'$filedir','$filename','$upfilename','$employeeid','$dotime','$linkcode','$helpid')";
					$db->query($sql_file);
				}
			}
		}
	}
}
?>