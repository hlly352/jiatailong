<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';
if($_POST['submit']){
	$employeeid = $_POST['employeeid'];
	if($employeeid){
		if($_FILES['file']['name']){
			$filedir = date("Ymd");
			$upload_path = "../upload/personnel/".$filedir."/";
			$upload = new upload();
			$upload->upload_file($upload_path);
			if(is_array($array_upload_file = $upload->array_upload_file)){
				$filename = $array_upload_file['upload_final_name'];
				$filepath = $upload_path.$filename;
				$big_filepath = $upload_path.'/B'.$filename;
				$image = new image();
				$image->smallimagedo($filepath,$filepath,98,140);
				$image->smallimagedo($filepath,$big_filepath,140,200);
				$sql_photo = "SELECT `photo_filedir`,`photo_filename` FROM `db_employee` WHERE `employeeid` = '$employeeid'";
				$result_photo = $db->query($sql_photo);
				if($result_photo->num_rows){
					$array_photo = $result_photo->fetch_assoc();
					$photo_filedir = $array_photo['photo_filedir'];
					$photo_filename = $array_photo['photo_filename'];
					$photo_filepath = "../upload/personnel/".$photo_filedir.'/'.$photo_filename;
					$photo_big_filepath = "../upload/personnel/".$photo_filedir.'/B'.$photo_filename;
					$upload->delfile($photo_filepath);
					$upload->delfile($photo_big_filepath);
				}
				$sql_update_photo = "UPDATE `db_employee` SET `photo_filedir` = '$filedir',`photo_filename` = '$filename' WHERE `employeeid` = '$employeeid'";
				$db->query($sql_update_photo);
			}
		}
	}
	header("location:".$_SERVER['HTTP_REFERER']);
}
?>