<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
require_once '../global_mysql_connect.php';
require_once '../function/function.php';
require_once '../class/upload.php';
require_once '../class/image.php';
require_once 'shell.php';
if($_POST['submit']){
	$mouldid = $_POST['mouldid'];
	$filedir = date("Ymd");
	$upfiledir = "../upload/file/".$filedir."/";
	$employeeid = $_SESSION['employee_info']['employeeid'];
	$dotime = fun_gettime();
	if($_FILES['file']['name']){
		$upload = new upload();
		$upload->upload_file($upfiledir);
		if(is_array($array_upfile = $upload->array_upload_file)){
			$filename = $array_upfile['upload_final_name'];
			$upfilename = $array_upfile['upload_name'];
			$filename_path = $upfiledir.'/'.$filename;
			$image = new image();
			$image->smallimagedo($filename_path,$filename_path,1077,710);
			$sql_image = "SELECT `filedir`,`filename` FROM `db_upload_file` WHERE `linkcode` = 'PEOR'";
			$result_image = $db->query($sql_image);
			if($result_image->num_rows){
				while($row_image = $result_image->fetch_assoc()){
					$image_filedir = $row_image['filedir'];
					$image_filename = $row_image['filename'];
					$image_filepath = "../upload/file/".$image_filedir.'/'.$image_filename;
					$upload->delfile($image_filepath);
				}
			}
			$sql_del = "DELETE FROM `db_upload_file` WHERE `linkcode` = 'PEOR'";
			$db->query($sql_del);
			$sql = "INSERT INTO `db_upload_file` (`fileid`,`filedir`,`filename`,`upfilename`,`employeeid`,`dotime`,`linkcode`) VALUES (NULL,'$filedir','$filename','$upfilename','$employeeid','$dotime','PEOR')";
			$db->query($sql);
		}
	}
	header("location:".$_SERVER['HTTP_REFERER']);
}
?>