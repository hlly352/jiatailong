<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';
if($_POST['submit']){
	$mould_dataid = $_POST['mould_dataid'];
	$filedir = date("Ymd");
	$upfiledir = "../upload/mould_image/".$filedir."/";
	if($_FILES['file']['name']){
		$upload = new upload();
		$upload->upload_file($upfiledir);
		if(is_array($array_upfile = $upload->array_upload_file)){
			$filename = $array_upfile['upload_final_name'];
			$filename_path = $upfiledir.'/'.$filename;
			$big_filename_path = $upfiledir.'/B'.$filename;
			$image = new image();
			$image->smallimagedo($filename_path,$big_filename_path,283,150);
			$image->smallimagedo($filename_path,$filename_path,85,45);
			$sql_image = "SELECT `image_filedir`,`image_filename` FROM `db_mould_data` WHERE `mould_dataid` = '$mould_dataid'";
			$result_image = $db->query($sql_image);
			if($result_image->num_rows){
				$array_image = $result_image->fetch_assoc();
				$image_filedir = $array_image['image_filedir'];
				$image_filename = $array_image['image_filename'];
				$image_filepath = "../upload/mould_image/".$image_filedir.'/'.$image_filename;
				$image_big_filepath = "../upload/mould_image/".$image_filedir.'/B'.$image_filename;
				$upload->delfile($image_filepath);
				$upload->delfile($image_big_filepath);
			}
			$sqlfile = "UPDATE `db_mould_data` SET `image_filedir` = '$filedir',`image_filename` = '$filename' WHERE `mould_dataid` = '$mould_dataid'";
			$db->query($sqlfile);
		}
	}
	header("location:".$_SERVER['HTTP_REFERER']);
}
?>