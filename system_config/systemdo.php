<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once 'shell.php';
if($_POST['submit']){
	$action = $_POST['action'];
	$system_type = $_POST['system_type'];
	$system_name = $_POST['system_name'];
	$system_dir = $_POST['system_dir'];
	$system_order = $_POST['system_order'];
	if($action == "add"){
		$sql = "INSERT INTO `db_system` (`systemid`,`system_type`,`system_name`,`system_dir`,`system_order`,`system_status`) VALUES (NULL,'$system_type','$system_name','$system_dir','$system_order',1)";
		$db->query($sql);
		if($systemid = $db->insert_id){
			header("location:system.php");
		}
	}elseif($action == "edit"){
		$systemid = $_POST['systemid'];
		$system_status = $_POST['system_status'];
		$sql = "UPDATE `db_system` SET `system_type` = '$system_type',`system_name` = '$system_name',`system_dir` = '$system_dir',`system_order` = '$system_order',`system_status` = '$system_status' WHERE `systemid` = '$systemid'";
		$db->query($sql);
		header("location:".$_SERVER['HTTP_REFERER']);
	}elseif($action == "del"){
		$array_systemid = $_POST['id'];
		$systemid = fun_convert_checkbox($array_systemid);
		//删除该系统下帮助文件的附件文件
		$sql_help_file = "SELECT `filedir`,`filename` FROM `db_upload_file` INNER JOIN `db_system_help` ON `db_system_help`.`helpid` = `db_upload_file`.`linkid` WHERE `db_upload_file`.`linkcode` = 'SH' AND `db_system_help`.`systemid` IN ($systemid)";
		$result_help_file = $db->query($sql_help_file);
		if($result_help_file->num_rows){
			while($row_help_file = $result_help_file->fetch_assoc()){
				$help_filedir = $row_help_file['filedir'];
				$help_filename = $row_help_file['filename'];
				$help_filepath = "../upload/file/".$help_filedir.'/'.$help_filename;
				fun_delfile($help_filepath);
			}
		}
		//删除帮助文件附件数据
		$sql_help_filelist = "DELETE `db_upload_file` FROM `db_upload_file` INNER JOIN `db_system_help` ON `db_system_help`.`helpid` = `db_upload_file`.`linkid`  WHERE `db_system_help`.`systemid` IN ($systemid) AND `db_upload_file`.`linkcode` = 'SH'";
		$db->query($sql_help_filelist);
		$sql_system_help = "DELETE FROM `db_system_help` WHERE `systemid` IN ($systemid)";
		$db->query($sql_system_help);
		//删除系统图标
		$sql_system_file = "SELECT `image_filedir`,`image_filename` FROM `db_system` WHERE `systemid` IN ($systemid)";
		$result_system_file = $db->query($sql_system_file);
		if($result_system_file->num_rows){
			while($row_system_file = $result_system_file->fetch_assoc()){
				$image_filedir = $row_system_file['image_filedir'];
				$image_filename = $row_system_file['image_filename'];
				$image_filepath = "../upload/system/".$image_filedir.'/'.$image_filename;
				fun_delfile($image_filepath);
			}
		}
		$sql_system = "DELETE FROM `db_system` WHERE `systemid` IN ($systemid)";
		$db->query($sql_system);
		header("location:".$_SERVER['HTTP_REFERER']);
	}
	if($systemid){
		if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/system/".$filedir."/";
			$upload = new upload();
			$upload->upload_file($upload_path);
			if(is_array($array_upload_file = $upload->array_upload_file)){
				if($action == "edit"){
					$sql_image = "SELECT `image_filedir`,`image_filename` FROM `db_system` WHERE `systemid` = '$systemid'";
					$result_image = $db->query($sql_image);
					if($result_image->num_rows){
						$array_image = $result_image->fetch_assoc();
						$image_filedir = $array_image['image_filedir'];
						$image_filename = $array_image['image_filename'];
						$image_filepath = "../upload/system/".$image_filedir.'/'.$image_filename;
						$upload->delfile($image_filepath);
					}	
				}
				$filename = $array_upload_file['upload_final_name'];
				$sql_update_image = "UPDATE `db_system` SET `image_filedir` = '$filedir',`image_filename` = '$filename' WHERE `systemid` = '$systemid'";
				$db->query($sql_update_image);
			}
		}
	}
}
?>